<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\ReviewImage;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|max:5120', // 5MB max per image
        ]);

        $customerId = Auth::guard('customer')->id();

        // Check if already reviewed
        if (ProductReview::where('product_id', $request->product_id)
            ->where('customer_id', $customerId)
            ->exists()) {
            return back()->with('error', __('store.product_detail.review_already_submitted'));
        }

        // Check if verified purchase
        $verifiedPurchase = Order::where('customer_id', $customerId)
            ->where('status', 'delivered')
            ->whereHas('details', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })
            ->exists();

        DB::beginTransaction();
        try {
            $review = ProductReview::create([
                'customer_id' => $customerId,
                'product_id' => $request->product_id,
                'rating' => $request->rating,
                'review' => $request->review,
                'is_approved' => 1,
                'verified_purchase' => $verifiedPurchase,
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('reviews', 'public');
                    ReviewImage::create([
                        'review_id' => $review->id,
                        'image_path' => $path,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', __('store.product_detail.review_success'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'envoi de l\'avis.');
        }
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful($id)
    {
        $customerId = Auth::guard('customer')->id();
        
        if (!$customerId) {
            return response()->json(['error' => 'Connexion requise'], 401);
        }

        $review = ProductReview::findOrFail($id);
        
        if ($review->markAsHelpful($customerId)) {
            return response()->json([
                'success' => true,
                'helpful_count' => $review->fresh()->helpful_count,
            ]);
        }

        return response()->json(['error' => 'Déjà voté'], 400);
    }

    /**
     * Get reviews for a product with filters
     */
    public function getProductReviews(Request $request, $productId)
    {
        $query = ProductReview::where('product_id', $productId)
            ->approved()
            ->with(['customer', 'images']);

        // Filter by rating
        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        // Filter by photos only
        if ($request->with_photos) {
            $query->withPhotos();
        }

        // Filter by verified purchase
        if ($request->verified) {
            $query->verified();
        }

        // Sort
        switch ($request->sort) {
            case 'helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'highest':
                $query->orderBy('rating', 'desc');
                break;
            case 'lowest':
                $query->orderBy('rating', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $reviews = $query->paginate(10);

        return response()->json([
            'reviews' => $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'customer_name' => $review->customer?->name ?? 'Anonyme',
                    'rating' => $review->rating,
                    'review' => $review->review,
                    'images' => $review->images->map(fn($img) => $img->url),
                    'helpful_count' => $review->helpful_count,
                    'verified_purchase' => $review->verified_purchase,
                    'created_at' => $review->created_at->diffForHumans(),
                ];
            }),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }
}

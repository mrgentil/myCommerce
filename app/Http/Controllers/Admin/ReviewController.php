<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductReview::with(['customer', 'product.translation', 'images']);

        if ($request->status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($request->status === 'approved') {
            $query->where('is_approved', true);
        }

        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        if ($request->has_photos) {
            $query->has('images');
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => ProductReview::count(),
            'pending' => ProductReview::where('is_approved', false)->count(),
            'with_photos' => ProductReview::has('images')->count(),
            'avg_rating' => round(ProductReview::avg('rating'), 1),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function show($id)
    {
        $review = ProductReview::with(['customer', 'product.translation', 'images'])->findOrFail($id);
        return view('admin.reviews.show', compact('review'));
    }

    public function approve($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Avis approuvé.');
    }

    public function reject($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->update(['is_approved' => false]);

        return redirect()->back()->with('success', 'Avis rejeté.');
    }

    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);
        
        // Delete associated images
        foreach ($review->images as $image) {
            \Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Avis supprimé.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:product_reviews,id',
        ]);

        $reviews = ProductReview::whereIn('id', $request->ids);

        switch ($request->action) {
            case 'approve':
                $reviews->update(['is_approved' => true]);
                $message = count($request->ids) . ' avis approuvés.';
                break;
            case 'reject':
                $reviews->update(['is_approved' => false]);
                $message = count($request->ids) . ' avis rejetés.';
                break;
            case 'delete':
                $reviews->delete();
                $message = count($request->ids) . ' avis supprimés.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}

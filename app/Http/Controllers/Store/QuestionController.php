<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductQuestion;
use App\Models\ProductAnswer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Get questions for a product
     */
    public function index(Request $request, $productId)
    {
        $query = ProductQuestion::where('product_id', $productId)
            ->public()
            ->with(['customer', 'answers']);

        // Filter
        if ($request->filter === 'answered') {
            $query->answered();
        } elseif ($request->filter === 'unanswered') {
            $query->unanswered();
        }

        // Sort
        switch ($request->sort) {
            case 'helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $questions = $query->paginate(10);

        return response()->json([
            'questions' => $questions->map(function ($q) {
                return [
                    'id' => $q->id,
                    'question' => $q->question,
                    'customer_name' => $q->customer?->name ?? 'Anonyme',
                    'helpful_count' => $q->helpful_count,
                    'created_at' => $q->created_at->diffForHumans(),
                    'answers' => $q->answers->map(function ($a) {
                        return [
                            'id' => $a->id,
                            'answer' => $a->answer,
                            'answerer_name' => $a->answerer_name,
                            'is_official' => $a->is_official,
                            'helpful_count' => $a->helpful_count,
                            'created_at' => $a->created_at->diffForHumans(),
                        ];
                    }),
                ];
            }),
            'pagination' => [
                'current_page' => $questions->currentPage(),
                'last_page' => $questions->lastPage(),
                'total' => $questions->total(),
            ],
        ]);
    }

    /**
     * Ask a question
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'question' => 'required|string|max:500',
        ]);

        $customerId = Auth::guard('customer')->id();
        
        if (!$customerId) {
            return response()->json(['error' => 'Connexion requise'], 401);
        }

        $question = ProductQuestion::create([
            'product_id' => $request->product_id,
            'customer_id' => $customerId,
            'question' => $request->question,
        ]);

        // Notify vendor
        $product = Product::find($request->product_id);
        if ($product && $product->vendor_id) {
            \App\Models\UserNotification::notifyVendor(
                $product->vendor_id,
                'message',
                'Nouvelle question',
                "Un client a posé une question sur {$product->name}",
                route('vendor.products.edit', $product->id)
            );
        }

        return response()->json([
            'success' => true,
            'question' => [
                'id' => $question->id,
                'question' => $question->question,
                'customer_name' => Auth::guard('customer')->user()->name,
                'created_at' => 'À l\'instant',
            ],
        ]);
    }

    /**
     * Answer a question (customer)
     */
    public function answer(Request $request, $questionId)
    {
        $request->validate([
            'answer' => 'required|string|max:1000',
        ]);

        $customerId = Auth::guard('customer')->id();
        
        if (!$customerId) {
            return response()->json(['error' => 'Connexion requise'], 401);
        }

        $question = ProductQuestion::findOrFail($questionId);

        $answer = ProductAnswer::create([
            'question_id' => $question->id,
            'answerer_type' => 'App\Models\Customer',
            'answerer_id' => $customerId,
            'answer' => $request->answer,
            'is_official' => false,
        ]);

        return response()->json([
            'success' => true,
            'answer' => [
                'id' => $answer->id,
                'answer' => $answer->answer,
                'answerer_name' => Auth::guard('customer')->user()->name,
                'is_official' => false,
                'created_at' => 'À l\'instant',
            ],
        ]);
    }

    /**
     * Mark question or answer as helpful
     */
    public function markHelpful(Request $request)
    {
        $request->validate([
            'type' => 'required|in:question,answer',
            'id' => 'required|integer',
        ]);

        if ($request->type === 'question') {
            ProductQuestion::where('id', $request->id)->increment('helpful_count');
        } else {
            ProductAnswer::where('id', $request->id)->increment('helpful_count');
        }

        return response()->json(['success' => true]);
    }
}

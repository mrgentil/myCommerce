<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductQuestion;
use App\Models\ProductAnswer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * List questions for vendor's products
     */
    public function index(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();

        $query = ProductQuestion::whereHas('product', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })->with(['customer', 'product.translation', 'answers']);

        // Filter
        if ($request->status === 'unanswered') {
            $query->unanswered();
        } elseif ($request->status === 'answered') {
            $query->answered();
        }

        // Product filter
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(15);

        $products = Product::where('vendor_id', $vendorId)
            ->with('translation')
            ->get();

        $stats = [
            'total' => ProductQuestion::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))->count(),
            'unanswered' => ProductQuestion::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))->unanswered()->count(),
        ];

        return view('vendor.questions.index', compact('questions', 'products', 'stats'));
    }

    /**
     * Answer a question (vendor - official)
     */
    public function answer(Request $request, $questionId)
    {
        $request->validate([
            'answer' => 'required|string|max:1000',
        ]);

        $vendorId = Auth::guard('vendor')->id();

        $question = ProductQuestion::whereHas('product', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })->findOrFail($questionId);

        // Check if vendor already answered
        $existingAnswer = ProductAnswer::where('question_id', $question->id)
            ->where('answerer_type', 'App\Models\Vendor')
            ->where('answerer_id', $vendorId)
            ->first();

        if ($existingAnswer) {
            $existingAnswer->update(['answer' => $request->answer]);
            $answer = $existingAnswer;
        } else {
            $answer = ProductAnswer::create([
                'question_id' => $question->id,
                'answerer_type' => 'App\Models\Vendor',
                'answerer_id' => $vendorId,
                'answer' => $request->answer,
                'is_official' => true,
            ]);
        }

        // Notify customer
        \App\Models\UserNotification::notifyCustomer(
            $question->customer_id,
            'message',
            'Réponse à votre question',
            "Le vendeur a répondu à votre question sur {$question->product->name}",
            route('product.show', $question->product->slug)
        );

        return response()->json([
            'success' => true,
            'message' => 'Réponse envoyée',
        ]);
    }

    /**
     * Delete a question
     */
    public function destroy($questionId)
    {
        $vendorId = Auth::guard('vendor')->id();

        $question = ProductQuestion::whereHas('product', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })->findOrFail($questionId);

        $question->delete();

        return response()->json(['success' => true]);
    }
}

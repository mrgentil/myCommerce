<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\DisputeMessage;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function index(Request $request)
    {
        $query = Dispute::with(['order', 'customer', 'vendor.shop']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $disputes = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'open' => Dispute::where('status', 'open')->count(),
            'escalated' => Dispute::where('status', 'escalated')->count(),
            'resolved' => Dispute::whereIn('status', ['resolved_refund', 'resolved_partial', 'resolved_no_refund'])->count(),
        ];

        return view('admin.disputes.index', compact('disputes', 'stats'));
    }

    public function show($id)
    {
        $dispute = Dispute::with([
            'order.details.product.translation',
            'customer',
            'vendor.shop',
            'messages',
            'evidence'
        ])->findOrFail($id);

        return view('admin.disputes.show', compact('dispute'));
    }

    public function addMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $dispute = Dispute::findOrFail($id);

        DisputeMessage::create([
            'dispute_id' => $dispute->id,
            'sender_type' => 'admin',
            'sender_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Message envoyé.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:under_review,awaiting_vendor,awaiting_customer,escalated',
        ]);

        $dispute = Dispute::findOrFail($id);
        $dispute->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }

    public function resolve(Request $request, $id)
    {
        $request->validate([
            'resolution' => 'required|in:resolved_refund,resolved_partial,resolved_no_refund',
            'refund_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $dispute = Dispute::findOrFail($id);
        $dispute->resolve($request->resolution, $request->refund_amount, $request->notes);

        return redirect()->back()->with('success', 'Litige résolu.');
    }
}

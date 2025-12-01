<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    /**
     * List vendor returns
     */
    public function index(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();

        $query = OrderReturn::where('vendor_id', $vendorId)
            ->with(['order', 'orderDetail.product.translation', 'customer']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $returns = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'pending' => OrderReturn::where('vendor_id', $vendorId)->where('status', 'pending')->count(),
            'approved' => OrderReturn::where('vendor_id', $vendorId)->where('status', 'approved')->count(),
            'shipped' => OrderReturn::where('vendor_id', $vendorId)->where('status', 'shipped')->count(),
            'completed' => OrderReturn::where('vendor_id', $vendorId)->whereIn('status', ['refunded', 'completed'])->count(),
        ];

        return view('vendor.returns.index', compact('returns', 'stats'));
    }

    /**
     * Show return details
     */
    public function show($id)
    {
        $vendorId = Auth::guard('vendor')->id();

        $return = OrderReturn::where('vendor_id', $vendorId)
            ->with(['order', 'orderDetail.product.translation', 'orderDetail.productVariant', 'customer', 'images'])
            ->findOrFail($id);

        return view('vendor.returns.show', compact('return'));
    }

    /**
     * Approve return
     */
    public function approve(Request $request, $id)
    {
        $vendorId = Auth::guard('vendor')->id();

        $return = OrderReturn::where('vendor_id', $vendorId)
            ->where('status', 'pending')
            ->findOrFail($id);

        $return->update([
            'status' => 'approved',
            'approved_at' => now(),
            'vendor_response' => $request->message,
        ]);

        return response()->json(['success' => true, 'message' => 'Retour approuvé']);
    }

    /**
     * Reject return
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $vendorId = Auth::guard('vendor')->id();

        $return = OrderReturn::where('vendor_id', $vendorId)
            ->where('status', 'pending')
            ->findOrFail($id);

        $return->update([
            'status' => 'rejected',
            'vendor_response' => $request->reason,
        ]);

        return response()->json(['success' => true, 'message' => 'Retour refusé']);
    }

    /**
     * Mark as received
     */
    public function markReceived($id)
    {
        $vendorId = Auth::guard('vendor')->id();

        $return = OrderReturn::where('vendor_id', $vendorId)
            ->where('status', 'shipped')
            ->findOrFail($id);

        $return->update([
            'status' => 'received',
            'received_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Article marqué comme reçu']);
    }

    /**
     * Process refund
     */
    public function refund(Request $request, $id)
    {
        $vendorId = Auth::guard('vendor')->id();

        $return = OrderReturn::where('vendor_id', $vendorId)
            ->whereIn('status', ['received', 'approved'])
            ->findOrFail($id);

        // In a real application, process the actual refund here
        // For now, just update the status

        $return->update([
            'status' => 'refunded',
            'refunded_at' => now(),
            'refund_amount' => $request->amount ?? $return->refund_amount,
        ]);

        return response()->json(['success' => true, 'message' => 'Remboursement effectué']);
    }
}

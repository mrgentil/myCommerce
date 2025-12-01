<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderReturn::with(['order', 'customer', 'vendor.shop', 'orderDetail.product.translation']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $returns = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'pending' => OrderReturn::where('status', 'pending')->count(),
            'approved' => OrderReturn::where('status', 'approved')->count(),
            'shipped' => OrderReturn::where('status', 'shipped')->count(),
            'completed' => OrderReturn::whereIn('status', ['refunded', 'completed'])->count(),
        ];

        return view('admin.returns.index', compact('returns', 'stats'));
    }

    public function show($id)
    {
        $return = OrderReturn::with([
            'order',
            'orderDetail.product.translation',
            'customer',
            'vendor.shop',
            'images'
        ])->findOrFail($id);

        return view('admin.returns.show', compact('return'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,shipped,received,refunded,completed',
            'admin_notes' => 'nullable|string',
        ]);

        $return = OrderReturn::findOrFail($id);
        
        $updateData = ['status' => $request->status];
        
        if ($request->admin_notes) {
            $updateData['admin_notes'] = $request->admin_notes;
        }

        if ($request->status === 'refunded') {
            $updateData['refunded_at'] = now();
        }

        $return->update($updateData);

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }
}

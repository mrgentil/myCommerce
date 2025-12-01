<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index()
    {
        return view('vendor.orders.index');
    }

    public function show($id)
    {
        $vendorId = Auth::guard('vendor')->id();

        $order = Order::whereHas('details.product', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })
            ->with(['details.product', 'customer', 'shippingAddress', 'billingAddress'])
            ->findOrFail($id);

        // Filter only vendor's products in the order
        $vendorItems = $order->details->filter(function ($detail) use ($vendorId) {
            return $detail->product && $detail->product->vendor_id == $vendorId;
        });

        $vendorTotal = $vendorItems->sum(function ($detail) {
            return $detail->quantity * $detail->price;
        });

        return view('vendor.orders.show', compact('order', 'vendorItems', 'vendorTotal'));
    }

    public function updateStatus(Request $request, $id)
    {
        $vendorId = Auth::guard('vendor')->id();

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled,delivered',
            'tracking_number' => 'nullable|string|max:100',
            'carrier' => 'nullable|string|max:50',
        ]);

        $order = Order::whereHas('details.product', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })->findOrFail($id);

        // Update order fields
        $updateData = ['status' => $request->status];
        
        if ($request->tracking_number) {
            $updateData['tracking_number'] = $request->tracking_number;
        }
        if ($request->carrier) {
            $updateData['carrier'] = $request->carrier;
        }

        // Set timestamps based on status
        if ($request->status === 'shipped' && !$order->shipped_at) {
            $updateData['shipped_at'] = now();
        } elseif ($request->status === 'delivered' && !$order->delivered_at) {
            $updateData['delivered_at'] = now();
        }

        $order->update($updateData);

        // Add tracking event
        $statusTitles = [
            'pending' => 'Commande en attente',
            'processing' => 'Commande en préparation',
            'shipped' => 'Commande expédiée',
            'completed' => 'Commande terminée',
            'delivered' => 'Commande livrée',
            'cancelled' => 'Commande annulée',
        ];

        $order->addTrackingEvent(
            $request->status,
            $statusTitles[$request->status] ?? 'Statut mis à jour',
            $request->note ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'status' => ucfirst($request->status)
        ]);
    }

    public function getData(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();

        $query = Order::whereHas('details.product', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })
            ->with(['details.product', 'customer'])
            ->latest();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('customer', function (Order $order) {
                if ($order->customer) {
                    return $order->customer->name;
                } elseif ($order->guest_email) {
                    return $order->guest_email.' (Invité)';
                }
                return 'N/A';
            })
            ->addColumn('order_date', fn (Order $order) => $order->created_at?->format('d/m/Y H:i'))
            ->addColumn('total_price', fn (Order $order) => number_format((float) $order->total_amount, 2, ',', ' ').' €')
            ->editColumn('status', function (Order $order) {
                $statusClasses = [
                    'pending' => 'warning',
                    'processing' => 'info',
                    'shipped' => 'primary',
                    'completed' => 'success',
                    'cancelled' => 'danger'
                ];
                $class = $statusClasses[$order->status] ?? 'secondary';
                $label = ucfirst($order->status);
                return '<span class="badge bg-'.$class.'">'.$label.'</span>';
            })
            ->addColumn('action', function (Order $order) {
                return '
                    <a href="'.route('vendor.orders.show', $order->id).'" class="btn btn-sm btn-outline-primary me-1" title="Voir">
                        <i class="bi bi-eye"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteOrder('.$order->id.')" title="Supprimer">
                        <i class="bi bi-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id')
            ->make(true);
    }

    public function destroy($id)
    {
        $vendorId = Auth::guard('vendor')->id();

        $order = Order::whereHas('details.product', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })->findOrFail($id);

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commande supprimée avec succès',
        ]);
    }
}

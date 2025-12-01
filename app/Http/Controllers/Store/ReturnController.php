<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderReturn;
use App\Models\OrderReturnImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.customer');
    }

    /**
     * List customer returns
     */
    public function index()
    {
        $customerId = Auth::guard('customer')->id();

        $returns = OrderReturn::where('customer_id', $customerId)
            ->with(['order', 'orderDetail.product.translation', 'vendor.shop'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('themes.xylo.customer.returns.index', compact('returns'));
    }

    /**
     * Show return request form
     */
    public function create($orderId)
    {
        $customerId = Auth::guard('customer')->id();

        $order = Order::where('customer_id', $customerId)
            ->where('status', 'delivered')
            ->with(['details.product.translation', 'details.productVariant', 'vendor'])
            ->findOrFail($orderId);

        // Check if return is still possible (within 14 days)
        $deliveredAt = $order->delivered_at ?? $order->updated_at;
        $returnDeadline = $deliveredAt->addDays(14);

        if (now()->gt($returnDeadline)) {
            return redirect()->back()->with('error', 'Le délai de retour de 14 jours est dépassé.');
        }

        $reasons = [
            'defective' => 'Produit défectueux',
            'wrong_item' => 'Mauvais article reçu',
            'not_as_described' => 'Non conforme à la description',
            'changed_mind' => 'Changement d\'avis',
            'too_late' => 'Livraison trop tardive',
            'damaged' => 'Produit endommagé',
            'other' => 'Autre raison',
        ];

        return view('themes.xylo.customer.returns.create', compact('order', 'reasons'));
    }

    /**
     * Store return request
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_detail_id' => 'required|exists:order_details,id',
            'type' => 'required|in:return,refund,exchange',
            'reason' => 'required|in:defective,wrong_item,not_as_described,changed_mind,too_late,damaged,other',
            'description' => 'required|string|max:1000',
            'quantity' => 'required|integer|min:1',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $customerId = Auth::guard('customer')->id();

        // Verify order belongs to customer
        $order = Order::where('customer_id', $customerId)->findOrFail($request->order_id);
        $orderDetail = OrderDetail::where('order_id', $order->id)->findOrFail($request->order_detail_id);

        // Check for existing return on same item
        $existingReturn = OrderReturn::where('order_detail_id', $orderDetail->id)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->first();

        if ($existingReturn) {
            return redirect()->back()->with('error', 'Une demande de retour existe déjà pour cet article.');
        }

        DB::beginTransaction();
        try {
            $return = OrderReturn::create([
                'order_id' => $order->id,
                'order_detail_id' => $orderDetail->id,
                'customer_id' => $customerId,
                'vendor_id' => $order->vendor_id,
                'type' => $request->type,
                'reason' => $request->reason,
                'description' => $request->description,
                'quantity' => min($request->quantity, $orderDetail->quantity),
                'refund_amount' => $orderDetail->price * min($request->quantity, $orderDetail->quantity),
            ]);

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('returns', 'public');
                    OrderReturnImage::create([
                        'order_return_id' => $return->id,
                        'image_path' => $path,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('customer.returns.show', $return->id)
                ->with('success', 'Demande de retour envoyée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Show return details
     */
    public function show($id)
    {
        $customerId = Auth::guard('customer')->id();

        $return = OrderReturn::where('customer_id', $customerId)
            ->with(['order', 'orderDetail.product.translation', 'orderDetail.productVariant', 'vendor.shop', 'images'])
            ->findOrFail($id);

        return view('themes.xylo.customer.returns.show', compact('return'));
    }

    /**
     * Update return tracking
     */
    public function updateTracking(Request $request, $id)
    {
        $request->validate([
            'return_tracking' => 'required|string|max:100',
        ]);

        $customerId = Auth::guard('customer')->id();

        $return = OrderReturn::where('customer_id', $customerId)
            ->where('status', 'approved')
            ->findOrFail($id);

        $return->update([
            'return_tracking' => $request->return_tracking,
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Numéro de suivi ajouté. Le vendeur sera notifié.');
    }

    /**
     * Cancel return request
     */
    public function cancel($id)
    {
        $customerId = Auth::guard('customer')->id();

        $return = OrderReturn::where('customer_id', $customerId)
            ->whereIn('status', ['pending'])
            ->findOrFail($id);

        $return->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Demande de retour annulée.');
    }
}

<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.customer');
    }

    /**
     * List customer orders
     */
    public function index()
    {
        $customerId = Auth::guard('customer')->id();

        $orders = Order::where('customer_id', $customerId)
            ->with(['details.product.translation', 'latestTracking'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('themes.xylo.customer.orders.index', compact('orders'));
    }

    /**
     * Show order details with tracking
     */
    public function show($id)
    {
        $customerId = Auth::guard('customer')->id();

        $order = Order::where('customer_id', $customerId)
            ->with(['details.product.translation', 'details.productVariant', 'vendor.shop', 'trackings'])
            ->findOrFail($id);

        return view('themes.xylo.customer.orders.show', compact('order'));
    }

    /**
     * Track order by number (public)
     */
    public function track(Request $request)
    {
        $orderNumber = $request->get('order');
        $email = $request->get('email');

        if (!$orderNumber) {
            return view('themes.xylo.track-order');
        }

        $order = Order::where('id', $orderNumber)
            ->where(function($q) use ($email) {
                $q->whereHas('customer', function($q2) use ($email) {
                    $q2->where('email', $email);
                })->orWhere('guest_email', $email);
            })
            ->with(['details.product.translation', 'trackings', 'vendor.shop'])
            ->first();

        if (!$order) {
            return view('themes.xylo.track-order', [
                'error' => 'Commande non trouvée. Vérifiez le numéro et l\'email.'
            ]);
        }

        return view('themes.xylo.track-order', compact('order'));
    }
}

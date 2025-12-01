<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\PaymentGateway\PaymentManager;
use App\Services\Store\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide');
        }

        $paymentGateways = PaymentGateway::with('configs')
            ->where('is_active', 1)
            ->get();

        $paypal = $paymentGateways->firstWhere('code', 'paypal');
        $paypalClientId = $paypal
            ? $paypal->getConfigValue('client_id', 'sandbox')
            : null;

        $stripe = $paymentGateways->firstWhere('code', 'stripe');
        $stripePublicKey = $stripe
            ? $stripe->getConfigValue('public_key', 'sandbox')
            : null;

        $subtotal = 0;
        $cartItems = [];

        foreach ($cart as $key => $item) {
            $product = Product::with(['translation', 'thumbnail'])->find($item['product_id']);
            $variant = isset($item['variant_id'])
                ? ProductVariant::find($item['variant_id'])
                : ProductVariant::where('product_id', $item['product_id'])->where('is_primary', true)->first();

            $cartItems[$key] = [
                'product' => $product,
                'variant' => $variant,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ];

            $subtotal += $item['price'] * $item['quantity'];
        }

        $shipping = 0;
        $total = $subtotal + $shipping;

        return view('themes.xylo.checkout', compact(
            'cart', 
            'cartItems',
            'subtotal', 
            'shipping', 
            'total', 
            'paymentGateways', 
            'paypalClientId',
            'stripePublicKey'
        ));
    }

    /**
     * Store the order (main checkout submission)
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'zipcode' => 'required|string|max:20',
            'gateway' => 'required|string',
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide');
        }

        // Calculate total
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = 0;
        $total = $subtotal + $shipping;

        DB::beginTransaction();
        try {
            // Get vendor_id from the first product
            $firstProduct = Product::find($cart[array_key_first($cart)]['product_id']);
            $vendorId = $firstProduct?->vendor_id;

            // Create order
            $order = Order::create([
                'customer_id' => Auth::guard('customer')->id(),
                'vendor_id' => $vendorId,
                'guest_email' => Auth::guard('customer')->check() ? null : $request->email,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $request->gateway,
                'payment_status' => 'pending',
                'shipping_address' => json_encode([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'address' => $request->address,
                    'suite' => $request->suite,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zipcode' => $request->zipcode,
                    'country' => $request->country,
                    'phone' => $request->phone,
                    'email' => $request->email,
                ]),
                'notes' => $request->notes,
            ]);

            // Create order details
            foreach ($cart as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Update stock
                if (isset($item['variant_id'])) {
                    ProductVariant::where('id', $item['variant_id'])->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();

            // Clear cart
            Session::forget('cart');

            // Handle payment based on gateway
            if ($request->gateway === 'cod') {
                // Cash on delivery - order is complete
                return redirect()->route('checkout.success', $order->id)
                    ->with('success', 'Commande passée avec succès !');
            }

            // For online payments, redirect to payment processing or success page
            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Commande passée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Process AJAX payment
     */
    public function process(Request $request)
    {
        $request->validate([
            'gateway' => 'required|string'
        ]);

        $gatewayCode = $request->input('gateway');
        $cart = Session::get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        try {
            $paymentService = PaymentManager::make($gatewayCode, 'sandbox');
            $order = $paymentService->createOrder($total, 'EUR');

            return response()->json([
                'success' => true,
                'gateway' => $gatewayCode,
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment process failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Order success page
     */
    public function success($orderId)
    {
        $order = Order::with('details.product')->findOrFail($orderId);
        return view('themes.xylo.checkout-success', compact('order'));
    }

    /**
     * PayPal success callback
     */
    public function paypalSuccess(Request $request, OrderService $orderService)
    {
        $orderId = $request->query('token');

        try {
            $paypal = PaymentManager::make('paypal', 'sandbox');
            $result = $paypal->captureOrder($orderId);

            if (($result['status'] ?? null) === 'COMPLETED') {
                $order = $orderService->createOrderFromPaypal($result);
                Session::forget('cart');

                return redirect()->route('checkout.success', $order->id)
                    ->with('success', 'Paiement effectué avec succès !');
            }

            return redirect()->route('checkout.index')
                ->with('error', 'Le paiement n\'a pas été complété.');
        } catch (\Exception $e) {
            Log::error('PayPal success error: ' . $e->getMessage());
            return redirect()->route('checkout.index')
                ->with('error', 'Erreur lors du paiement: ' . $e->getMessage());
        }
    }

    /**
     * PayPal cancel callback
     */
    public function paypalCancel()
    {
        return redirect()->route('checkout.index')
            ->with('error', 'Paiement annulé.');
    }
}

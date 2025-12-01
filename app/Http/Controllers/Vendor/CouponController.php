<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $vendorId = Auth::guard('vendor')->id();
        
        $coupons = VendorCoupon::where('vendor_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->get();

        $activeCoupons = $coupons->where('is_active', true)->count();
        $expiredCoupons = $coupons->filter(fn($c) => $c->end_date && $c->end_date->isPast())->count();
        $totalUsage = $coupons->sum('usage_count');

        return view('vendor.coupons.index', compact('coupons', 'activeCoupons', 'expiredCoupons', 'totalUsage'));
    }

    public function create()
    {
        return view('vendor.coupons.create');
    }

    public function store(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:vendor_coupons,code',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_customer' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['vendor_id'] = $vendorId;
        $validated['code'] = $validated['code'] ?: strtoupper(Str::random(8));
        $validated['is_active'] = $request->has('is_active');

        VendorCoupon::create($validated);

        return redirect()->route('vendor.coupons.index')
            ->with('success', 'Coupon créé avec succès !');
    }

    public function edit($id)
    {
        $vendorId = Auth::guard('vendor')->id();
        $coupon = VendorCoupon::where('vendor_id', $vendorId)->findOrFail($id);

        return view('vendor.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $vendorId = Auth::guard('vendor')->id();
        $coupon = VendorCoupon::where('vendor_id', $vendorId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:vendor_coupons,code,' . $id,
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_customer' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()->route('vendor.coupons.index')
            ->with('success', 'Coupon mis à jour avec succès !');
    }

    public function destroy($id)
    {
        $vendorId = Auth::guard('vendor')->id();
        $coupon = VendorCoupon::where('vendor_id', $vendorId)->findOrFail($id);

        $coupon->delete();

        return response()->json(['success' => true, 'message' => 'Coupon supprimé']);
    }

    public function toggle($id)
    {
        $vendorId = Auth::guard('vendor')->id();
        $coupon = VendorCoupon::where('vendor_id', $vendorId)->findOrFail($id);

        $coupon->update(['is_active' => !$coupon->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $coupon->is_active,
            'message' => $coupon->is_active ? 'Coupon activé' : 'Coupon désactivé'
        ]);
    }
}

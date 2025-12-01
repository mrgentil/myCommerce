<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('vendor.auth.login');
    }

    public function showRegistrationForm()
    {
        return view('vendor.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'shop_name' => 'required|string|max:255|unique:shops,name',
            'shop_description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Create vendor (pending approval)
            $vendor = Vendor::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'phone' => $request->phone,
                'status' => 'pending', // Requires admin approval
            ]);

            // Create shop for vendor
            Shop::create([
                'vendor_id' => $vendor->id,
                'name' => $request->shop_name,
                'slug' => Str::slug($request->shop_name),
                'description' => $request->shop_description,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('vendor.login')
                ->with('success', __('vendor.registration_success'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Registration failed: ' . $e->getMessage())->withInput();
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $vendor = Vendor::where('email', $request->email)->first();

        if ($vendor && $vendor->status === 'pending') {
            return back()->with('error', __('vendor.account_pending'));
        }

        if ($vendor && $vendor->status === 'rejected') {
            return back()->with('error', __('vendor.account_rejected'));
        }

        if (Auth::guard('vendor')->attempt($request->only('email', 'password'))) {
            return redirect()->route('vendor.dashboard');
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        Auth::guard('vendor')->logout();

        return redirect()->route('vendor.login');
    }

    public function dashboard()
    {
        return view('vendor.dashboard');
    }
}

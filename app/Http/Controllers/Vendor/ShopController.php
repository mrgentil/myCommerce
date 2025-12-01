<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    /**
     * Show the shop configuration form.
     */
    public function edit()
    {
        $vendor = Auth::guard('vendor')->user();
        $shop = $vendor->shop;

        return view('vendor.shop.edit', compact('vendor', 'shop'));
    }

    /**
     * Update the shop configuration.
     */
    public function update(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            // Hero fields
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_button_text' => 'nullable|string|max:100',
            'hero_button_link' => 'nullable|string|max:255',
            'hero_background' => 'nullable|string|max:500',
            'hero_text_color' => 'nullable|string|max:50',
        ]);

        $shop = $vendor->shop;

        // If shop doesn't exist, create it
        if (!$shop) {
            $shop = new Shop();
            $shop->vendor_id = $vendor->id;
            $shop->status = 'pending'; // New shops need approval
        }

        $shop->name = $validated['name'];
        $shop->slug = Str::slug($validated['name']);
        $shop->description = $validated['description'] ?? null;
        $shop->phone = $validated['phone'] ?? null;
        $shop->address = $validated['address'] ?? null;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($shop->logo && Storage::disk('public')->exists($shop->logo)) {
                Storage::disk('public')->delete($shop->logo);
            }
            $shop->logo = $request->file('logo')->store('shops/logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner
            if ($shop->banner && Storage::disk('public')->exists($shop->banner)) {
                Storage::disk('public')->delete($shop->banner);
            }
            $shop->banner = $request->file('banner')->store('shops/banners', 'public');
        }

        // Hero fields
        $shop->hero_title = $validated['hero_title'] ?? null;
        $shop->hero_subtitle = $validated['hero_subtitle'] ?? null;
        $shop->hero_button_text = $validated['hero_button_text'] ?? 'Voir les produits';
        $shop->hero_button_link = $validated['hero_button_link'] ?? null;
        $shop->hero_background = $validated['hero_background'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        $shop->hero_text_color = $validated['hero_text_color'] ?? '#ffffff';

        $shop->save();

        return back()->with('success', 'Boutique mise à jour avec succès !');
    }

    /**
     * Remove logo.
     */
    public function removeLogo()
    {
        $vendor = Auth::guard('vendor')->user();
        $shop = $vendor->shop;

        if ($shop && $shop->logo) {
            if (Storage::disk('public')->exists($shop->logo)) {
                Storage::disk('public')->delete($shop->logo);
            }
            $shop->logo = null;
            $shop->save();
        }

        return response()->json(['success' => true, 'message' => 'Logo supprimé.']);
    }

    /**
     * Remove banner.
     */
    public function removeBanner()
    {
        $vendor = Auth::guard('vendor')->user();
        $shop = $vendor->shop;

        if ($shop && $shop->banner) {
            if (Storage::disk('public')->exists($shop->banner)) {
                Storage::disk('public')->delete($shop->banner);
            }
            $shop->banner = null;
            $shop->save();
        }

        return response()->json(['success' => true, 'message' => 'Bannière supprimée.']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::first();
        return view('admin.site-settings.index', compact('settings'));
    }

    public function edit()
    {
        $settings = SiteSetting::first();
        return view('admin.site-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            'logo_dark' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg,svg|max:512',
        ]);

        $settings = SiteSetting::first();

        $data = [
            'site_name' => $request->site_name,
            'tagline' => $request->tagline,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address' => $request->address,
            'footer_text' => $request->footer_text,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            $data['logo'] = $request->file('logo')->store('site', 'public');
        }

        // Handle logo dark upload
        if ($request->hasFile('logo_dark')) {
            if ($settings->logo_dark && Storage::disk('public')->exists($settings->logo_dark)) {
                Storage::disk('public')->delete($settings->logo_dark);
            }
            $data['logo_dark'] = $request->file('logo_dark')->store('site', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($settings->favicon && Storage::disk('public')->exists($settings->favicon)) {
                Storage::disk('public')->delete($settings->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('site', 'public');
        }

        $settings->update($data);

        return redirect()->route('admin.site-settings.index')->with('success', 'Paramètres mis à jour avec succès !');
    }

    /**
     * Remove logo
     */
    public function removeLogo(Request $request)
    {
        $settings = SiteSetting::first();
        $type = $request->input('type', 'logo');

        if ($type === 'logo' && $settings->logo) {
            Storage::disk('public')->delete($settings->logo);
            $settings->update(['logo' => null]);
        } elseif ($type === 'logo_dark' && $settings->logo_dark) {
            Storage::disk('public')->delete($settings->logo_dark);
            $settings->update(['logo_dark' => null]);
        } elseif ($type === 'favicon' && $settings->favicon) {
            Storage::disk('public')->delete($settings->favicon);
            $settings->update(['favicon' => null]);
        }

        return response()->json(['success' => true]);
    }
}

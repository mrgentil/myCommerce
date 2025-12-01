<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorBadge;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorBadgeController extends Controller
{
    public function index()
    {
        $badges = VendorBadge::withCount('vendors')->orderBy('priority', 'desc')->get();
        return view('admin.badges.index', compact('badges'));
    }

    public function create()
    {
        return view('admin.badges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:20',
            'description' => 'nullable|string',
            'priority' => 'integer|min:0',
        ]);

        VendorBadge::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon,
            'color' => $request->color,
            'description' => $request->description,
            'priority' => $request->priority ?? 0,
            'requirements' => $request->requirements ? json_decode($request->requirements, true) : null,
        ]);

        return redirect()->route('admin.badges.index')->with('success', 'Badge créé.');
    }

    public function edit($id)
    {
        $badge = VendorBadge::findOrFail($id);
        return view('admin.badges.edit', compact('badge'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:20',
        ]);

        $badge = VendorBadge::findOrFail($id);
        $badge->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'color' => $request->color,
            'description' => $request->description,
            'priority' => $request->priority ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', 'Badge mis à jour.');
    }

    public function destroy($id)
    {
        VendorBadge::findOrFail($id)->delete();
        return redirect()->route('admin.badges.index')->with('success', 'Badge supprimé.');
    }

    /**
     * Assign badges to vendors
     */
    public function assignments()
    {
        $vendors = Vendor::with(['shop', 'badges'])->paginate(20);
        $badges = VendorBadge::active()->orderBy('priority', 'desc')->get();

        return view('admin.badges.assignments', compact('vendors', 'badges'));
    }

    public function assignBadge(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'badge_id' => 'required|exists:vendor_badges,id',
        ]);

        $vendor = Vendor::findOrFail($request->vendor_id);
        
        // Check if already assigned
        if (!$vendor->badges()->where('badge_id', $request->badge_id)->exists()) {
            $vendor->badges()->attach($request->badge_id, ['awarded_at' => now()]);
        }

        return redirect()->back()->with('success', 'Badge attribué.');
    }

    public function removeBadge(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'badge_id' => 'required|exists:vendor_badges,id',
        ]);

        $vendor = Vendor::findOrFail($request->vendor_id);
        $vendor->badges()->detach($request->badge_id);

        return redirect()->back()->with('success', 'Badge retiré.');
    }

    /**
     * Verify a vendor
     */
    public function verifyVendor($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        // Auto-assign verified badge
        $verifiedBadge = VendorBadge::where('slug', 'verified')->first();
        if ($verifiedBadge && !$vendor->badges()->where('badge_id', $verifiedBadge->id)->exists()) {
            $vendor->badges()->attach($verifiedBadge->id, ['awarded_at' => now()]);
        }

        return redirect()->back()->with('success', 'Vendeur vérifié.');
    }
}

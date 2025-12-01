<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    /**
     * Display a listing of hero slides.
     */
    public function index()
    {
        $slides = HeroSlide::ordered()->get();
        return view('admin.hero-slides.index', compact('slides'));
    }

    /**
     * Show the form for creating a new slide.
     */
    public function create()
    {
        return view('admin.hero-slides.create');
    }

    /**
     * Store a newly created slide.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'button_text' => 'required|string|max:100',
            'button_link' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'background_color' => 'nullable|string|max:500',
            'text_color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'status' => 'nullable',
        ]);

        $validated['status'] = $request->has('status');
        $validated['order'] = $validated['order'] ?? HeroSlide::max('order') + 1;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('hero-slides', 'public');
        }

        HeroSlide::create($validated);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Slide créé avec succès !');
    }

    /**
     * Show the form for editing a slide.
     */
    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.hero-slides.edit', compact('heroSlide'));
    }

    /**
     * Update the specified slide.
     */
    public function update(Request $request, HeroSlide $heroSlide)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'button_text' => 'required|string|max:100',
            'button_link' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'background_color' => 'nullable|string|max:500',
            'text_color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'status' => 'nullable',
        ]);

        $validated['status'] = $request->has('status');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($heroSlide->image && Storage::disk('public')->exists($heroSlide->image)) {
                Storage::disk('public')->delete($heroSlide->image);
            }
            $validated['image'] = $request->file('image')->store('hero-slides', 'public');
        } else {
            // Don't overwrite image if not uploaded
            unset($validated['image']);
        }

        $heroSlide->update($validated);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Slide mis à jour avec succès !');
    }

    /**
     * Remove the specified slide.
     */
    public function destroy(HeroSlide $heroSlide)
    {
        if ($heroSlide->image && Storage::disk('public')->exists($heroSlide->image)) {
            Storage::disk('public')->delete($heroSlide->image);
        }

        $heroSlide->delete();

        return response()->json([
            'success' => true,
            'message' => 'Slide supprimé avec succès !'
        ]);
    }

    /**
     * Update slide order via AJAX.
     */
    public function updateOrder(Request $request)
    {
        $order = $request->input('order', []);
        
        foreach ($order as $index => $id) {
            HeroSlide::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle slide status.
     */
    public function toggleStatus(HeroSlide $heroSlide)
    {
        $heroSlide->update(['status' => !$heroSlide->status]);

        return response()->json([
            'success' => true,
            'status' => $heroSlide->status,
            'message' => $heroSlide->status ? 'Slide activé' : 'Slide désactivé'
        ]);
    }
}

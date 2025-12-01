<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    /**
     * Display a page by its slug.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $translation = $page->translation();

        if (!$translation) {
            abort(404);
        }

        return view('themes.xylo.page', compact('page', 'translation'));
    }
}

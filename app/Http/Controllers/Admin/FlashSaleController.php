<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    public function index()
    {
        $flashSales = FlashSale::withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function create()
    {
        $products = Product::with('translation', 'primaryVariant')
            ->where('status', true)
            ->get();

        return view('admin.flash-sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'banner' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'description', 'starts_at', 'ends_at']);
        
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('flash-sales', 'public');
        }

        $flashSale = FlashSale::create($data);

        return redirect()->route('admin.flash-sales.edit', $flashSale->id)
            ->with('success', 'Vente flash créée. Ajoutez maintenant des produits.');
    }

    public function edit($id)
    {
        $flashSale = FlashSale::with('products.product.translation')->findOrFail($id);
        $products = Product::with('translation', 'primaryVariant')
            ->where('status', true)
            ->get();

        return view('admin.flash-sales.edit', compact('flashSale', 'products'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $flashSale = FlashSale::findOrFail($id);
        
        $data = $request->only(['title', 'description', 'starts_at', 'ends_at']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('flash-sales', 'public');
        }

        $flashSale->update($data);

        return redirect()->back()->with('success', 'Vente flash mise à jour.');
    }

    public function addProduct(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'sale_price' => 'required|numeric|min:0',
            'quantity_limit' => 'nullable|integer|min:1',
        ]);

        $flashSale = FlashSale::findOrFail($id);

        FlashSaleProduct::updateOrCreate(
            [
                'flash_sale_id' => $flashSale->id,
                'product_id' => $request->product_id,
            ],
            [
                'sale_price' => $request->sale_price,
                'quantity_limit' => $request->quantity_limit,
            ]
        );

        return redirect()->back()->with('success', 'Produit ajouté à la vente flash.');
    }

    public function removeProduct($id, $productId)
    {
        FlashSaleProduct::where('flash_sale_id', $id)
            ->where('product_id', $productId)
            ->delete();

        return redirect()->back()->with('success', 'Produit retiré.');
    }

    public function destroy($id)
    {
        FlashSale::findOrFail($id)->delete();
        return redirect()->route('admin.flash-sales.index')->with('success', 'Vente flash supprimée.');
    }
}

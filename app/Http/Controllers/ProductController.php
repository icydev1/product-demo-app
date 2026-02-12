<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::latest()->get();
        return view('welcome', compact('products'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'manufacturer_date' => 'required|date',
            'expiry_date' => 'required|date|after:manufacturer_date',
            // 'copies' => 'required|integer|min:1|max:1000',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product added successfully!');
    }


    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'manufacturer_date' => 'required|date',
            'expiry_date' => 'required|date|after:manufacturer_date',
            // 'copies' => 'required|integer|min:1|max:1000',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }


    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private function getStatus($stock)
    {
        if ($stock <= 0) {
            return 'sold_out';
        }

        if ($stock <= 10) {
            return 'limited_stock';
        }

        return 'in_stock';
    }

    public function index(Request $request)
    {
        $query = Product::latest();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->get());
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $request->file('image')->store('products', 'public');

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image' => $path,
            'image_url' => asset('storage/' . $path),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:women,men,beauty,accessories,footwear',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'stock' => 'required|integer|min:0',
        ]);

        $stock = (int) $request->stock;

        $product = Product::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'image' => $request->image,
            'price' => $request->price,
            'rating' => $request->rating ?? 5.0,
            'stock' => $stock,
            'status' => $this->getStatus($stock),
        ]);

        return response()->json([
            'message' => 'Product added successfully',
            'product' => $product,
        ], 201);
    }

    public function show($id)
    {
        return response()->json(Product::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:women,men,beauty,accessories,footwear',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'stock' => 'required|integer|min:0',
        ]);

        $stock = (int) $request->stock;

        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'image' => $request->image,
            'price' => $request->price,
            'rating' => $request->rating ?? 5.0,
            'stock' => $stock,
            'status' => $this->getStatus($stock),
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && !str_starts_with($product->image, 'http')) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}
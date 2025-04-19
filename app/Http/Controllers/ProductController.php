<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return view('products.admin.index', compact('products'));
    }

    public function index2()
    {
        $products = Product::all();
        return view('products.staff.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:products',
            'stock' => 'required|integer',
            'price' => 'required|integer',
            'image' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        Product::create([
            'name' => $request->name,
            'stock' => $request->stock,
            'price' => $request->price,
            'image' => $imagePath,
        ]);

        session()->flash('success', ['type' => 'created', 'message' => 'Data successfully created.']);

        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    public function up($id)
    {
        $product = Product::findOrFail($id);
        return view('products.admin.update', compact('product'));
    }

    // Mengupdate produk
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|unique:products,name,' . $id,
            'price' => 'sometimes|required|integer|min:0',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // Simpan gambar baru
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        // Update hanya kolom yang boleh diubah
        $product->update($request->only(['name', 'price']));

        // Simpan perubahan path gambar jika ada file baru
        if ($request->hasFile('image')) {
            $product->image = $imagePath;
            $product->save();
        }

        // Set session success message
        session()->flash('success', ['type' => 'created', 'message' => 'Data successfully updated.']);

        // Redirect back to the products page
        return redirect()->route('products.index');
    }

    public function upStock($id)
    {
        $product = Product::findOrFail($id);
        return view('products.admin.updatestock', compact('product'));
    }

    // Mengupdate stok produk
    public function updateStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update(['stock' => $request->stock]);

        // Set session success message
        session()->flash('success', [
            'type' => 'updated',
            'message' => 'Stock successfully updated.'
        ]);

        // Redirect ke halaman index produk
        return redirect()->route('products.index');
    }

    // Menghapus produk
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return view('products.admin.index');
        }

        Storage::disk('public')->delete($product->image);
        $product->delete();

        // Set session success message
        session()->flash('success', ['type' => 'deleted', 'message' => 'Data successfully deleted.']);

        // Redirect back to the products page
        return redirect()->route('products.index');
    }
}

@extends('layouts.sidebar')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <div class="flex justify-between items-center">
        <nav class="text-gray-500 text-sm flex items-center space-x-2">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-1 hover:text-gray-700">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span>Home</span>
            </a>
            <span>/</span>
            <a href="{{ route('products.index') }}" class="hover:text-gray-700">Product</a>
            <span>/</span>
            <a href="{{ route('products.up', $product->id) }}" class="text-gray-900 font-semibold">Update Product</a>
        </nav>
        <div class="relative">
                <button id="profileMenu" class="rounded-full bg-orange-300 p-2">
                    <i data-lucide="lightbulb" class="w-6 h-6 text-white"></i>
                </button>
                <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white shadow-md rounded-lg p-2 hidden">
                    <a href="" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                    <a href="" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
                </div>
        </div>
    </div>

    <h1 class="text-2xl font-bold mt-4">Update Product</h1>

    <div class="bg-white p-6 mt-4 rounded-lg shadow-md">
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-2 gap-4">
                <!-- Nama Product -->
                <div>
                    <label for="name" class="block text-sm font-semibold">Name Product</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" class="mt-1 w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image Product -->
                <div>
                    <label class="block text-sm font-semibold">Image Product</label>
                    <input type="file" name="image" accept="image/*" class="mt-1 w-full border border-gray-300 px-3 py-2 rounded-md">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="Current Image" class="mt-2 w-32 h-32 object-cover">
                    @endif
                    @error('image')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Price Product -->
                <div>
                    <label for="price" class="block text-sm font-semibold">Price</label>
                    <input type="text" id="price" name="price" value="{{ old('price', $product->price) }}" class="mt-1 w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-blue-500 focus:border-blue-500">
                    @error('price')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-semibold">Stock</label>
                    <input type="number" id="stock" name="stock" 
                        value="{{ old('stock', $product->stock) }}" 
                        class="mt-1 w-full border border-gray-300 px-3 py-2 rounded-md bg-gray-400 text-white cursor-not-allowed focus:ring-0 focus:border-gray-600" 
                        readonly>
                    @error('stock')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

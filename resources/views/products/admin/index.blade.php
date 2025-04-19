@extends('layouts.sidebar')

@section('content')

    <!-- Main Content -->
    <main class="flex-1 p10 ml-4">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <nav class="text-gray-500 text-sm flex items-center space-x-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-1 hover:text-gray-700">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    <span>Home</span>
                </a>
                <span>/</span>
                <span class="text-gray-900 font-semibold">Product</span>
            </nav>

            <div class="relative">
                <button id="profileMenu" class="rounded-full bg-orange-300 p-2">
                    <i data-lucide="lightbulb" class="w-6 h-6 text-white"></i>
                </button>
                <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white shadow-md rounded-lg p-2 hidden">
                    <a href="" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                    <a href="{{ route('logout') }}" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
                </div>
            </div>
        </div>

        <!-- Success Alert -->
        @if(session('success') && is_array(session('success')))
            @php
                $alert = session('success'); // This is now an array
            @endphp

            <div class="p-4 rounded-md mt-4 
                @if($alert['type'] == 'created') bg-green-500 @elseif($alert['type'] == 'deleted') bg-red-500 @endif text-white">
                {{ $alert['message'] }}
            </div>

            <script>
                // Automatically refresh the page after 3 seconds
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
            </script>
        @endif

        <!-- Product List -->
        <div class="bg-white p-8 shadow-md rounded-lg mt-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-semibold flex items-center space-x-2">
                    <i data-lucide="box"></i> <span>Products</span>
                </h2>
                <a href="{{ route('products.create') }}" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center space-x-2">
                    <i data-lucide="plus-circle"></i> <span>Create Product</span>
                </a>
            </div>

            <table class="w-full mt-6 border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-3 text-left">#</th>
                        <th class="border p-3 text-left">Image</th>
                        <th class="border p-3 text-left">Product Name</th>
                        <th class="border p-3 text-left">Stock</th>
                        <th class="border p-3 text-left">Price</th>
                        <th class="border p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr class="border">
                        <td class="border p-3">{{ $loop->iteration }}</td>
                        <td class="border p-3">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-md">
                        </td>
                        <td class="border p-3">{{ $product->name }}</td>
                        <td class="border p-3">{{ $product->stock }}</td>
                        <td class="border p-3">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="border p-3 flex justify-center space-x-3 whitespace-nowrap">
                            <a href="{{ route('products.up', $product->id) }}" class="text-blue-500 hover:text-blue-700">
                                <i data-lucide="edit"></i>
                            </a>
                            <button onclick="confirmDelete({{ $product->id }})" class="text-red-500 hover:text-red-700" >
                                <i data-lucide="trash"></i>
                            </button>
                            <a href="{{ route('products.up-stock', $product->id) }}" class="text-green-600 hover:text-green-800 font-semibold">
                                Update Stock
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg w-1/3">
            <h3 class="text-lg font-semibold">Confirm Deletion</h3>
            <p class="my-4">Are you sure you want to delete this product?</p>
            <div class="flex justify-end space-x-3">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-400 text-white rounded-md">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md">Delete</button>
                </form>
            </div>
        </div>
    </div>

<script>
    lucide.createIcons();

    // Toggle Profile Menu
    document.getElementById('profileMenu').addEventListener('click', function () {
        document.getElementById('profileDropdown').classList.toggle('hidden');
    });

    // Confirm Deletion
    function confirmDelete(productId) {
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = '/products/delete/' + productId;

        deleteModal.classList.remove('hidden');
    }

    // Cancel Deletion
    document.getElementById('cancelDelete').addEventListener('click', function() {
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.classList.add('hidden');
    });
</script>

@endsection

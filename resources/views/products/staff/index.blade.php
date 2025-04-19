@extends('layouts.sidebar')

@section('content')

    <!-- Main Content -->
    <main class="flex-1 p10 ml-4">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <nav class="text-gray-500 text-sm flex items-center space-x-2">
                <a href="" class="flex items-center space-x-1 hover:text-gray-700">
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
            </div>

            <table class="w-full mt-6 border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-3 text-left">#</th>
                        <th class="border p-3 text-left">Image</th>
                        <th class="border p-3 text-left">Product Name</th>
                        <th class="border p-3 text-left">Stock</th>
                        <th class="border p-3 text-left">Price</th>
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
    </div>

<script>
    lucide.createIcons();

    // Toggle Profile Menu
    document.getElementById('profileMenu').addEventListener('click', function () {
        document.getElementById('profileDropdown').classList.toggle('hidden');
    });

</script>

@endsection

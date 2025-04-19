@extends('layouts.sidebar')

@section('content')

<main class="flex-1 p-8 ml-4">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <!-- Breadcrumb -->
        <nav class="text-gray-500 text-sm flex items-center space-x-2">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-1 hover:text-gray-700">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span>Dashboard</span>
            </a>
            <span>/</span>
            <a href="{{ route('transactions.index') }}" class="hover:text-gray-700">Purchase</a>
            <span>/</span>
            <a href="{{ route('transactions.create') }}" class="text-gray-900 font-semibold">Create Purchase</a>
        </nav>

        <!-- Profile -->
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

    <!-- Product Form -->
    <div class="bg-white p-8 shadow-md rounded-lg mt-6">
        <h2 class="text-2xl font-semibold mb-6">Purchase</h2>

        <form action="{{ route('transactions.confirm') }}" method="POST">
            @method('POST')
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="border rounded-lg shadow-sm p-4 flex flex-col items-center" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-md">
                        <h5 class="text-lg font-semibold mt-3">{{ $product->name }}</h5>
                        <p class="text-sm text-gray-500">Stok: <span class="product-stock">{{ $product->stock }}</span></p>
                        <p class="text-sm text-gray-600">Rp. {{ number_format($product->price, 0, ',', '.') }}</p>

                        <div class="flex items-center gap-2 mt-2">
                            <button type="button" class="minus-btn px-2 py-1 border rounded text-gray-700 hover:bg-gray-100">-</button>
                            <input type="number" name="quantities[{{ $product->id }}]" value="0" min="0" max="{{ $product->stock }}" class="qty-input w-16 text-center border rounded">
                            <button type="button" class="plus-btn px-2 py-1 border rounded text-gray-700 hover:bg-gray-100">+</button>
                        </div>

                        <p class="text-sm mt-2">Sub Total: <span class="subtotal">Rp 0</span></p>
                    </div>
                @endforeach
            </div>

            <div class="text-right mt-8">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Next</button>
            </div>
        </form>
    </div>
</main>

<script>
    lucide.createIcons();

    document.getElementById('profileMenu').addEventListener('click', function () {
        document.getElementById('profileDropdown').classList.toggle('hidden');
    });

    document.querySelectorAll('[data-price]').forEach(card => {
        const minusBtn = card.querySelector('.minus-btn');
        const plusBtn = card.querySelector('.plus-btn');
        const qtyInput = card.querySelector('.qty-input');
        const subtotalSpan = card.querySelector('.subtotal');

        const price = parseInt(card.dataset.price);
        const stock = parseInt(card.dataset.stock);

        const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(value);

        const updateSubtotal = () => {
            let qty = parseInt(qtyInput.value) || 0;
            if (qty < 0) qty = 0;
            if (qty > stock) qty = stock;

            qtyInput.value = qty;
            subtotalSpan.textContent = 'Rp ' + formatRupiah(qty * price);
        };

        minusBtn.addEventListener('click', () => {
            let qty = parseInt(qtyInput.value) || 0;
            if (qty > 0) {
                qtyInput.value = qty - 1;
                updateSubtotal();
            }
        });

        plusBtn.addEventListener('click', () => {
            let qty = parseInt(qtyInput.value) || 0;
            if (qty < stock) {
                qtyInput.value = qty + 1;
                updateSubtotal();
            } else {
                alert('Melebihi stok tersedia!');
            }
        });

        qtyInput.addEventListener('input', updateSubtotal);
    });
</script>

@endsection

@extends('layouts.sidebar')

@section('content')

<main class="flex-1 p-8 ml-4 font-['Inter']">

    <!-- Breadcrumb -->
    <div class="flex justify-between items-center">
        <nav class="text-gray-500 text-sm flex items-center space-x-2">
            <a href="/" class="flex items-center space-x-1 hover:text-gray-700">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span>Home</span>
            </a>
            <span>/</span>
            <a href="{{ route('transactions.create') }}" class="text-gray-900">Create</a>
            <span>/</span>
            <span class="text-gray-900">Confirm</span>
            <span>/</span>
            <span class="text-gray-900 font-semibold">Member Purchase</span>
        </nav>
    </div>

    <!-- Card -->
    <div class="bg-white shadow-md rounded-lg p-8 mt-6">
        <h3 class="text-xl font-semibold mb-6 flex items-center space-x-2">
            <i data-lucide="shopping-cart"></i>
            <span>Penjualan - Member</span>
        </h3>

        <!-- Tabel Produk -->
        <div>
            <h5 class="text-lg font-semibold mb-4">Daftar Produk</h5>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Nama Produk</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Qty</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Harga</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if(isset($items) && count($items))
                            @foreach ($items as $item)
                                <tr>
                                    <td class="px-4 py-2">{{ $item->product->name }}</td>
                                    <td class="px-4 py-2">{{ $item->quantity }}</td>
                                    <td class="px-4 py-2">Rp. {{ number_format($item->price) }}</td>
                                    <td class="px-4 py-2">Rp. {{ number_format($item->subtotal) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-center text-gray-500">Belum ada item ditambahkan.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4 space-y-1">
                <p class="text-base font-semibold">Total Harga: <span class="text-gray-800">Rp. {{ number_format($total_price) }}</span></p>
                <p class="text-base font-semibold">Total Bayar: <span class="text-gray-800">Rp. {{ number_format($total_payment) }}</span></p>
                <p class="text-base font-semibold">Kembalian: <span class="text-gray-800">Rp. {{ number_format($change) }}</span></p>
            </div>
        </div>

        <!-- Informasi Member -->
        <div class="mt-8">
            <h5 class="text-lg font-semibold mb-4">Informasi Member</h5>
            <form method="POST" action="{{ route('transactions.receiption', $transaction->id) }}">
                @csrf
                <input type="hidden" name="member_id" value="{{ $member?->id }}">
                <input type="hidden" name="total_price" value="{{ $total_price }}">
                <input type="hidden" name="total_payment" value="{{ $total_payment }}">
                <input type="hidden" name="use_points" value="0" id="usePointsInput">

                <div class="space-y-4">
                    <div>
                        <label class="block mb-1 font-medium">Nama Member</label>
                        <input type="text" class="w-full border rounded-md px-4 py-2" 
                            value="{{ $member?->name }}" name="name">
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">No Telepon</label>
                        <input type="text" class="w-full border border-gray-300 rounded-md px-4 py-2 bg-gray-100" 
                            value="{{ $member?->no_phone }}" readonly>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Total Poin</label>
                        <input type="text" class="w-full border border-gray-300 rounded-md px-4 py-2 bg-gray-100" 
                            value="{{ $points ?? 0 }}" readonly>
                    </div>

                    @if ($member && !$member->wasRecentlyCreated)
                        <div class="flex items-center mt-2">
                            <input class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500" type="checkbox" id="usePoints">
                            <label for="usePoints" class="text-sm font-medium text-gray-700">Gunakan poin</label>
                        </div>
                    @else
                        <div class="mt-2 text-sm text-red-600">
                            Poin tidak dapat digunakan pada pembelanjaan pertama.
                        </div>
                    @endif
                </div>

                <div class="pt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-semibold">
                        Selanjutnya
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    lucide.createIcons();

    // Checkbox sinkronisasi dengan hidden input
    document.getElementById('usePoints')?.addEventListener('change', function () {
        document.getElementById('usePointsInput').value = this.checked ? 1 : 0;
    });
</script>

@endsection

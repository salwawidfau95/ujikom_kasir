@extends('layouts.sidebar')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-8 ml-4">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <nav class="text-gray-500 text-sm flex items-center space-x-2">
            <a href="" class="flex items-center space-x-1 hover:text-gray-700">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span>Home</span>
            </a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">Purchase</span>
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

    <div class="bg-white p-8 shadow-md rounded-lg mt-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold flex items-center space-x-2">
                <i data-lucide="shopping-cart"></i>
                <span>Purchase</span>
            </h2>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center mt-6 mb-4 gap-4">
            
            <a href="{{ route('transactions.export') }}" class="inline-block bg-[#1A4D2E] hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg text-sm shadow transition">
                Export Purchase (.xlsx)
            </a>

        </div>

        <table class="w-full mt-6 border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-3 text-left">#</th>
                    <th class="border p-3 text-left">Customer Name</th>
                    <th class="border p-3 text-left">Created At</th>
                    <th class="border p-3 text-right">Total Price</th>
                    <th class="border p-3 text-right">Created by</th>
                    <th class="border p-3 text-center"></th>
                </tr>
            </thead>
            <tbody class="text-center text-sm text-gray-700">
                @foreach ($transactions as $transaction)
                    <tr class="border-b border-gray-200">
                        <td class="py-3 px-2">
                            {{ optional($transaction->member)->name ?? 'NON-MEMBER' }}
                        </td>
                        <td class="py-3 px-2">
                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d') }}
                        </td>
                        <td class="py-3 px-2 text-right">
                            Rp. {{ number_format($transaction->total_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-2 text-right">
                            {{ $transaction->user->username ?? '-' }}
                        </td>
                        <td class="py-3 px-2 flex justify-center space-x-2">
                            <!-- Modal trigger -->
                            <button onclick="openModal('modal-{{ $transaction->id }}')" class="bg-yellow-400 text-black px-3 py-1 rounded hover:bg-yellow-500 text-sm">
                                Show
                            </button>
                            <a href="{{ route('transactions.receipt', $transaction->id) }}" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                                Download Proof
                            </a>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div id="modal-{{ $transaction->id }}" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
                        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-lg p-6 md:p-8 mx-4 md:mx-0 space-y-6">
                            <!-- Modal Header -->
                            <div class="flex items-center justify-between border-b pb-4">
                                <h2 class="text-xl md:text-2xl font-bold text-gray-800 flex items-center gap-2">
                                    <i data-lucide="file-text" class="w-5 h-5"></i> Purchase Detail
                                </h2>
                                <button onclick="closeModal('modal-{{ $transaction->id }}')" class="text-gray-500 hover:text-red-600 text-xl font-bold">
                                    &times;
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p><span class="font-medium">Status Member:</span> {{ $transaction->member_id ? 'Member' : 'Non Member' }}</p>
                                    <p><span class="font-medium">Nama Pelanggan:</span> {{ $transaction->member->name ?? '-' }}</p>
                                    <p><span class="font-medium">No Telp:</span> {{ $transaction->member->no_phone ?? '-' }}</p>
                                    <p><span class="font-medium">Poin:</span> {{ $transaction->member->point ?? 0 }}</p>
                                    <p><span class="font-medium">Bergabung Sejak:</span> {{ $transaction->member_id ? \Carbon\Carbon::parse($transaction->member->created_at)->format('Y-m-d') : '-' }}</p>
                                </div>
                            </div>

                            <div>
                                <p class="font-semibold mb-1">Produk:</p>
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($transaction->detail as $detail)
                                        <li>
                                            {{ $detail->product->name }} - Qty: {{ $detail->quantity }} - Harga: Rp {{ number_format($detail->price, 0, ',', '.') }} - Subtotal: Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <p><span class="font-medium">Total Harga:</span> Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</p>
                                <p><span class="font-medium">Total Bayar:</span> Rp {{ number_format($transaction->total_payment, 0, ',', '.') }}</p>
                                <p><span class="font-medium">Kembalian:</span> Rp {{ number_format($transaction->change, 0, ',', '.') }}</p>
                            </div>

                            <p class="text-sm text-gray-500">Dibuat pada: {{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i:s') }} oleh <strong>{{ $transaction->user->username ?? '-' }}</strong></p>

                            </div>

                            <!-- Modal Footer -->

                        </div>
                    </div>

                @endforeach
            </tbody>
        </table>
    </div>
</main>

<script>
    lucide.createIcons();

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    document.getElementById('profileMenu').addEventListener('click', function () {
        document.getElementById('profileDropdown').classList.toggle('hidden');
    });
</script>
@endsection

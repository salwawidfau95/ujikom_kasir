@extends('layouts.sidebar')

@section('content')

<main class="p-6">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Payment</h2>
            <div>
                <a href="{{ route('transactions.receipt2', $transaction->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md mr-2">Download</a>
                <a href="{{ route('transactions.index2') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md">Back</a>
            </div>
        </div>

        <div class="flex justify-between text-gray-600 mb-4">
            <div></div>
            <div class="text-right">
                <p><strong>Invoice</strong> – #{{ $transaction->transaction_code }}</p>
                <p>{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-t border-b border-gray-200">
                <thead class="text-gray-500 uppercase text-sm">
                    <tr>
                        <th class="py-2">Product</th>
                        <th class="py-2">Price</th>
                        <th class="py-2">Quantity</th>
                        <th class="py-2 text-right">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                    <tr class="border-t border-gray-100">
                        <td class="py-2">{{ $item->product->name }}</td>
                        <td class="py-2">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="py-2">{{ $item->quantity }}</td>
                        <td class="py-2 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-6 bg-gray-100 rounded-md p-4">
            <div>
                <p class="text-gray-500 text-sm">Used Point</p>
                <p class="text-lg">{{ $used_point }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Casheer</p>
                <p class="text-lg">{{ $user->username }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Change</p>
                <p class="text-lg font-semibold text-blue-600">Rp {{ number_format($change, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <div class="bg-gray-800 text-white px-6 py-4 rounded-md w-64">
                <p class="text-sm">TOTAL</p>
                <p class="text-2xl font-bold">Rp {{ number_format($total_price, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</main>

<script>
    lucide.createIcons();
</script>

@endsection

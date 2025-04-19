@extends('layouts.sidebar')

@section('content')

<!-- Main Content -->
<main class="flex-1 p-8 ml-4">
    <div class="flex justify-between items-center">
        <nav class="text-gray-500 text-sm flex items-center space-x-2">
            <a href="" class="flex items-center space-x-1 hover:text-gray-700">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span>Home</span>
            </a>
            <span>/</span>
            <a href="{{ route('transactions.create') }}" class="text-gray-900">Create</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">Confirm Purchase</span>
        </nav>
        <div class="relative">
            <button id="profileMenu" class="rounded-full bg-orange-300 p-2">
                <i data-lucide="lightbulb" class="w-6 h-6 text-white"></i>
            </button>
            <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white shadow-md rounded-lg p-2 hidden z-50">
                <a href="#" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white p-8 shadow-md rounded-lg mt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Produk -->
            <div>
                <h3 class="text-xl font-semibold mb-4 flex items-center space-x-2">
                    <i data-lucide="shopping-bag"></i>
                    <span>Selected Products</span>
                </h3>
                <div class="space-y-2">
                    @foreach ($selectedProducts as $item)
                        <div class="flex justify-between border-b pb-1">
                            <span>{{ $item['name'] }}</span>
                            <span>Rp {{ number_format($item['price'], 0, ',', '.') }} x {{ $item['qty'] }}</span>
                        </div>
                    @endforeach
                </div>
                <hr class="my-4">
                <div class="flex justify-between font-semibold text-lg">
                    <span>Total</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Konfirmasi -->
            <div>
                <h3 class="text-xl font-semibold mb-4 flex items-center space-x-2">
                    <i data-lucide="check-circle"></i>
                    <span>Payment Confirmation</span>
                </h3>

                <form action="{{ route('transactions.finalize') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="member_status" class="block font-medium mb-1">
                            Status Member 
                            <span class="text-red-500 font-normal">Can also create new members</span>
                        </label>
                        <select name="member_status" id="member_status" class="form-select w-full border border-gray-300 rounded-md p-2" required>
                            <option value="non-member">Non Member</option>
                            <option value="member">Member</option>
                        </select>
                    </div>

                    <!-- Input no_phone muncul hanya jika member dipilih -->
                    <div id="memberPhoneContainer" class="hidden">
                        <label for="no_phone" class="block font-medium mb-1">Member Telephone Number</label>
                        <input type="text" name="no_phone" id="no_phone" class="w-full border border-gray-300 rounded-md p-2" placeholder="08xxxxxxxxxx">
                        <p id="memberNotFound" class="text-sm text-red-500 mt-1 hidden">Member not defined</p>
                    </div>

                    <!-- Hidden input untuk simpan member_id -->
                    <input type="hidden" name="member_id" id="member_id">

                    <div>
                        <label for="total_payment" class="block font-medium mb-1" >Total Payment</label>
                        <input type="number" name="total_payment" class="w-full border border-gray-300 rounded-md p-2" placeholder="Masukkan jumlah pembayaran" required>

                        @error('total_payment')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    lucide.createIcons();

    document.getElementById('profileMenu').addEventListener('click', function () {
        document.getElementById('profileDropdown').classList.toggle('hidden');
    });

    const memberStatus = document.getElementById('member_status');
    const phoneContainer = document.getElementById('memberPhoneContainer');
    const phoneInput = document.getElementById('no_phone');
    const memberIdInput = document.getElementById('member_id');
    const notFoundText = document.getElementById('memberNotFound');

    if (phoneInput) {
        phoneInput.addEventListener('blur', function () {
            const no_phone = phoneInput.value.trim();
            if (!no_phone) return;

            fetch(`/members/find-by-phone?no_phone=${encodeURIComponent(no_phone)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.member_id) {
                        memberIdInput.value = data.member_id;
                        notFoundText.classList.add('hidden');
                    } else {
                        memberIdInput.value = '';
                        notFoundText.classList.remove('hidden');
                    }
                })
                .catch(() => {
                    memberIdInput.value = '';
                    notFoundText.classList.remove('hidden');
                });
        });
    }

    memberStatus.addEventListener('change', function () {
        if (this.value === 'member') {
            phoneContainer.classList.remove('hidden');
            phoneInput.setAttribute('required', 'required');
        } else {
            phoneContainer.classList.add('hidden');
            phoneInput.removeAttribute('required');
            memberIdInput.value = '';
            notFoundText.classList.add('hidden');
        }
    });

    const totalPaymentInput = document.getElementById('total_payment');
    totalPaymentInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        e.target.value = 'Rp ' + rupiah;
    });

    // Handle form submission to send value as an integer
    const form = document.querySelector('form');
    form.addEventListener('submit', function (event) {
        const totalPayment = totalPaymentInput.value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
        totalPaymentInput.value = parseInt(totalPayment); // Convert to integer
    });
});

</script>

@endsection

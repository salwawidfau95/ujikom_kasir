@extends('layouts.sidebar')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6 font-[Inter]">
    <div class="flex justify-between items-center">
        <div class="bg-white p-6 rounded-3xl shadow-sm border mb-6">
            <h2 class="text-2xl font-semibold text-dark mb-2">
                Selamat Datang, {{ ucfirst($user->role) }}!
            </h2>
        </div>
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

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button class="text-xl font-bold leading-none focus:outline-none" onclick="this.parentElement.remove();">&times;</button>
        </div>
    @endif

    {{-- ALERT INFO (jika ada) --}}
    @if(session('info'))
        <div class="mb-4 p-4 rounded-lg bg-blue-100 text-blue-800 flex justify-between items-center">
            <span>{{ session('info') }}</span>
            <button class="text-xl font-bold leading-none focus:outline-none" onclick="this.parentElement.remove();">&times;</button>
        </div>
    @endif

    @if ($user->role === 'staff')
        <div class="bg-white p-6 rounded-3xl shadow-sm border">
            <div class="bg-gray-100 rounded-lg overflow-hidden">
                <div class="text-center bg-gray-200 py-3 font-semibold text-gray-700">
                    Total Penjualan Hari Ini
                </div>
                <div class="py-8 text-center">
                    <p class="text-5xl font-bold text-gray-800">
                        {{ $totalTransactionsToday }}
                    </p>
                    <p class="mt-2 text-sm text-gray-500">Jumlah total penjualan yang terjadi hari ini.</p>
                </div>
                <div class="text-center text-xs text-gray-400 bg-gray-100 py-2">
                    Terakhir diperbarui: {{ $lastUpdated?->created_at }}
                </div>
            </div>
        </div>
    @elseif ($user->role === 'admin')
        <div class="row g-4">
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-3xl shadow-sm border h-full">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Persentase Penjualan Produk</h3>
                    <div class="mx-auto d-flex justify-content-center">
                        <canvas id="overallPieChart" class="max-w-full h-auto" style="max-width: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="bg-white p-4 rounded-3xl shadow-sm border h-full overflow-auto">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Grafik Penjualan per Hari</h3>
                    <div class="min-w-[600px]">
                        <canvas id="salesChart" height="300"></canvas>
                    </div>
                    @if($salesPerDay->isEmpty())
                        <p class="text-sm text-gray-500 mt-2">Belum ada data untuk ditampilkan dalam grafik.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produkLabels = @json($productSales->pluck('name'));
        const produkTotals = @json($productSales->pluck('total_sold'));
        const pieColors = [
            '#f87171', '#60a5fa', '#fbbf24', '#34d399', '#c084fc',
            '#f97316', '#ec4899', '#22d3ee', '#818cf8', '#fde68a',
            '#86efac', '#fca5a5'
        ];

        const ctxPie = document.getElementById('overallPieChart')?.getContext('2d');
        if (ctxPie && produkLabels.length > 0) {
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: produkLabels,
                    datasets: [{
                        data: produkTotals,
                        backgroundColor: pieColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percent = ((value / total) * 100).toFixed(2) + '%';
                                    return `${context.label}: ${value} (${percent})`;
                                }
                            }
                        }
                    }
                }
            });
        }

        const rawSales = @json($salesPerDay);
        const today = new Date();
        const last7Days = Array.from({ length: 7 }, (_, i) => {
            const d = new Date(today);
            d.setDate(d.getDate() - (6 - i));
            return d.toISOString().slice(0, 10);
        });

        const salesMap = Object.fromEntries(rawSales.map(item => [item.date, item.total_sales]));
        const labels = last7Days;
        const totalPenjualan = last7Days.map(date => salesMap[date] || 0);

        const ctx = document.getElementById('salesChart')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Transaksi',
                        data: totalPenjualan,
                        backgroundColor: 'rgba(59, 130, 246, 0.3)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false },
                    },
                    scales: {
                        x: {
                            ticks: {
                                callback: function (value, index, ticks) {
                                    const dateStr = this.getLabelForValue(value);
                                    const [year, month, day] = dateStr.split("-");
                                    return `${day}/${month}`;
                                },
                                maxRotation: 60,
                                minRotation: 45
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const profileButton = document.getElementById('profileMenu');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileButton && profileDropdown) {
            profileButton.addEventListener('click', function (e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!profileDropdown.contains(e.target) && !profileButton.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }
    });
</script>
@endpush

@extends('layouts.app')

@section('title', 'Dashboard - PT Sahabat Agro Group')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Selamat Datang, {{ $userName ?? 'User' }}!</h2>
        <p class="text-green-100 mt-1">PT Sahabat Agro Group — {{ date('d F Y') }}</p>
    </div>

    <!-- Filter Section (safe) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="kebun" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kebun</label>
                <select name="kebun" id="kebun" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Semua Kebun</option>
                    @foreach(($kebunList ?? []) as $k)
                        <option value="{{ $k }}" {{ request('kebun') == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="divisi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Divisi</label>
                <select name="divisi" id="divisi" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Semua Divisi</option>
                    @foreach(($divisiList ?? []) as $d)
                        <option value="{{ $d }}" {{ request('divisi') == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bulan</label>
                <select name="bulan" id="bulan" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Bulan Ini</option>
                    @foreach(($bulanList ?? []) as $b)
                        <option value="{{ strtoupper($b) }}" {{ strtoupper((string)request('bulan')) === strtoupper((string)$b) ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun</label>
                <select name="tahun" id="tahun" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Tahun Ini</option>
                    @for($y = ($yearNow ?? (int)date('Y'))+1; $y >= ($yearNow ?? (int)date('Y'))-5; $y--)
                        <option value="{{ $y }}" {{ (string)request('tahun') === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    <i class="fas fa-filter mr-2"></i>
                    Filter Data
                </button>
                <a href="{{ request()->fullUrlWithQuery(['charts' => ($enableCharts ?? false) ? '0' : '1']) }}"
                   class="whitespace-nowrap bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 font-medium py-2 px-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-chart-line mr-2"></i>
                    {{ ($enableCharts ?? false) ? 'Sembunyikan Grafik' : 'Tampilkan Grafik' }}
                </a>
            </div>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border">
            <h3 class="font-semibold mb-1">Produksi PKS (Bulan)</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['total_produksi'] ?? 0, 2) }} <span class="text-sm font-medium">kg</span></p>
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border">
            <h3 class="font-semibold mb-1">Refraksi</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['refraksi_kg'] ?? 0, 2) }} <span class="text-sm">kg</span> <span class="text-gray-500 text-base">• {{ number_format($monthlyMetrics['refraksi_persen'] ?? 0, 2) }}%</span></p>
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border">
            <h3 class="font-semibold mb-1">Restan</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['restan_jjg'] ?? 0, 2) }} <span class="text-sm">JJG</span> <span class="text-gray-500 text-base">• {{ number_format($monthlyMetrics['restan_persen'] ?? 0, 2) }}%</span></p>
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border">
            <h3 class="font-semibold mb-1">JJG / PKK (Bulan)</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['jjg_per_pkk'] ?? 0, 2) }}</p>
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border">
            <h3 class="font-semibold mb-1">Ha / HK (Bulan)</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['ha_per_hk'] ?? 0, 2) }}</p>
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border">
            <h3 class="font-semibold mb-1">Ton / HK (Bulan)</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['ton_per_hk'] ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Charts (gated) -->
    @if(!empty($enableCharts))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold mb-3">Produksi PKS vs Budget Harian</h3>
            <canvas id="chartPksBudget" height="120"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold mb-3">AKP Harian (%)</h3>
            <canvas id="chartAkp" height="120"></canvas>
        </div>
    </div>
    <script>
        (function() {
            try {
                // Data from server
                var dailySeries = @json($chartData['daily_pks_budget'] ?? []);
                var akpSeries = @json($chartData['akp_daily'] ?? []);

                // Labels as day of month (1..31)
                var labels = dailySeries.map(function(d) {
                    var parts = (d.tanggal_panen || '').split('-');
                    return parts.length === 3 ? parts[2] : '';
                });

                // Datasets
                var pksData = dailySeries.map(function(d){ return Number(d.total_pks || 0); });
                var budgetData = dailySeries.map(function(d){ return Number(d.total_budget || 0); });
                var akpData = akpSeries.map(function(d){ return Number(d.akp_pct || 0); });

                // Chart 1: PKS vs Budget
                var ctx1 = document.getElementById('chartPksBudget');
                if (ctx1 && ctx1.getContext) {
                    new Chart(ctx1.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'PKS (kg)',
                                    data: pksData,
                                    borderColor: 'rgb(34,197,94)',
                                    backgroundColor: 'rgba(34,197,94,0.1)',
                                    tension: 0.3,
                                    pointRadius: 2,
                                },
                                {
                                    label: 'Budget (kg)',
                                    data: budgetData,
                                    borderColor: 'rgb(59,130,246)',
                                    backgroundColor: 'rgba(59,130,246,0.1)',
                                    tension: 0.3,
                                    pointRadius: 2,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true }
                            },
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }

                // Chart 2: AKP %
                var ctx2 = document.getElementById('chartAkp');
                if (ctx2 && ctx2.getContext) {
                    new Chart(ctx2.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'AKP (%)',
                                    data: akpData,
                                    borderColor: 'rgb(234,88,12)',
                                    backgroundColor: 'rgba(234,88,12,0.1)',
                                    tension: 0.3,
                                    pointRadius: 2,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, ticks: { callback: function(v){ return v + '%'; } } }
                            },
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }
            } catch (e) { /* swallow */ }
        })();
    </script>
    @endif
</div>
@endsection

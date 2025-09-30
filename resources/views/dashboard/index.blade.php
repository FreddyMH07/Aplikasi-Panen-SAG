@extends('layouts.app')

@section('title', 'Dashboard - PT Sahabat Agro Group')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    @php
        // Threshold-based classes
    $acv = (float)($todayMetrics['acv_prod'] ?? 0);
    // Thresholds: <70 red; 70–<85 yellow; 85–≤110 green; >110 blue
    $acvColor = $acv > 110 ? 'text-blue-600 dark:text-blue-300'
           : ($acv >= 85 ? 'text-green-600 dark:text-green-300'
           : ($acv >= 70 ? 'text-yellow-600 dark:text-yellow-300'
           : 'text-red-600 dark:text-red-300'));
    $acvBg    = $acv > 110 ? 'bg-blue-100 dark:bg-blue-900'
           : ($acv >= 85 ? 'bg-green-100 dark:bg-green-900'
           : ($acv >= 70 ? 'bg-yellow-100 dark:bg-yellow-900'
           : 'bg-red-100 dark:bg-red-900'));

        $ref = (float)($todayMetrics['refraksi_persen'] ?? 0);
        $refColor = $ref <= 1 ? 'text-green-600 dark:text-green-300' : ($ref <= 2 ? 'text-yellow-600 dark:text-yellow-300' : 'text-red-600 dark:text-red-300');
        $refBg    = $ref <= 1 ? 'bg-green-100 dark:bg-green-900' : ($ref <= 2 ? 'bg-yellow-100 dark:bg-yellow-900' : 'bg-red-100 dark:bg-red-900');

    $acvMonthly = (float)($monthlyMetrics['acv_prod'] ?? 0);
    $acvMonthlyColor = $acvMonthly > 110 ? 'text-blue-600 dark:text-blue-300'
               : ($acvMonthly >= 85 ? 'text-green-600 dark:text-green-300'
               : ($acvMonthly >= 70 ? 'text-yellow-600 dark:text-yellow-300'
               : 'text-red-600 dark:text-red-300'));

    // Positive vs zero coloring for HK & Produksi
    $hk = (int)($todayMetrics['total_tk'] ?? 0);
    $hkColor = $hk > 0 ? 'text-blue-600 dark:text-blue-300' : 'text-yellow-600 dark:text-yellow-300';
    $hkBg    = $hk > 0 ? 'bg-blue-100 dark:bg-blue-900' : 'bg-yellow-100 dark:bg-yellow-900';

    $prod = (float)($todayMetrics['total_produksi'] ?? 0);
    $prodColor = $prod > 0 ? 'text-green-600 dark:text-green-300' : 'text-yellow-600 dark:text-yellow-300';
    $prodBg    = $prod > 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-yellow-100 dark:bg-yellow-900';

    $prodMonthly = (float)($monthlyMetrics['total_produksi'] ?? 0);
    $prodMonthlyColor = $prodMonthly > 0 ? 'text-green-600 dark:text-green-300' : 'text-yellow-600 dark:text-yellow-300';
    @endphp
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h2>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-seedling text-6xl text-green-200"></i>
            </div>
        </div>
    </div>
    <!-- Tagline + Date separated with distinct color -->
    <div class="bg-blue-50 dark:bg-blue-900/40 border border-blue-200 dark:border-blue-800 rounded-xl p-4 text-blue-700 dark:text-blue-200">
        <p class="font-medium">PT Sahabat Agro Group - Sistem Report Panen Sawit Digital - {{ date('d F Y') }}</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="kebun" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kebun</label>
                <select name="kebun" id="kebun" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Semua Kebun</option>
                    @foreach($kebunList as $k)
                        <option value="{{ $k }}" {{ request('kebun') == $k ? 'selected' : '' }}>
                            {{ $k }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="divisi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Divisi</label>
                <select name="divisi" id="divisi" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Semua Divisi</option>
                    @foreach($divisiList as $d)
                        <option value="{{ $d }}" {{ request('divisi') == $d ? 'selected' : '' }}>
                            {{ $d }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bulan</label>
                @php($bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'])
                <select name="bulan" id="bulan" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Bulan Ini</option>
                    @foreach($bulanList as $b)
                        <option value="{{ strtoupper($b) }}" {{ strtoupper(request('bulan')) === strtoupper($b) ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun</label>
                @php($yearNow = (int)date('Y'))
                <select name="tahun" id="tahun" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Tahun Ini</option>
                    @for($y = $yearNow+1; $y >= $yearNow-5; $y--)
                        <option value="{{ $y }}" {{ (string)request('tahun') === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    <i class="fas fa-filter mr-2"></i>
                    Filter Data
                </button>
            </div>
        </form>
    </div>
    
    <!-- Today's Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- BJR Hari Ini -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">BJR Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ number_format($todayMetrics['bjr'], 2) }} 
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Berat Janjang Rata-rata</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <i class="fas fa-weight text-blue-600 dark:text-blue-300"></i>
                </div>
            </div>
        </div>
        
        <!-- AKP Hari Ini -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">AKP Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ number_format($todayMetrics['akp'] * 100, 2) }}%
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Angka Kerapatan Panen</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <i class="fas fa-chart-line text-green-600 dark:text-green-300"></i>
                </div>
            </div>
        </div>
        
        <!-- HK Hari Ini -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">HK Hari Ini</p>
                    <p class="text-2xl font-bold {{ $hkColor }}">
                        {{ number_format($todayMetrics['total_tk']) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tenaga Kerja Panen</p>
                </div>
                <div class="p-3 {{ $hkBg }} rounded-full">
                    <i class="fas fa-users {{ $hkColor }}"></i>
                </div>
            </div>
        </div>
        
        <!-- ACV Prod Harian -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">ACV Prod Harian</p>
                    <p class="text-2xl font-bold {{ $acvColor }}">
                        {{ number_format($todayMetrics['acv_prod'], 2) }}%
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Achievement vs Budget</p>
                </div>
                <div class="p-3 {{ $acvBg }} rounded-full">
                    <i class="fas fa-percentage {{ $acvColor }}"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Produksi Hari Ini -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Produksi Hari Ini</p>
                    <p class="text-2xl font-bold {{ $prodColor }}">
                        {{ number_format($todayMetrics['total_produksi'], 2) }} kg
                    </p>
                </div>
                <div class="p-3 {{ $prodBg }} rounded-full">
                    <i class="fas fa-seedling {{ $prodColor }}"></i>
                </div>
            </div>
        </div>
        
        <!-- Selisih -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Selisih Timbang</p>
                    <p class="text-2xl font-bold {{ $todayMetrics['selisih'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $todayMetrics['selisih'] >= 0 ? '+' : '' }}{{ number_format($todayMetrics['selisih'], 2) }} kg
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($todayMetrics['selisih_persen'] ?? 0, 2) }}%</p>
                </div>
                <div class="p-3 {{ $todayMetrics['selisih'] >= 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-full">
                    <i class="fas fa-balance-scale {{ $todayMetrics['selisih'] >= 0 ? 'text-green-600 dark:text-green-300' : 'text-red-600 dark:text-red-300' }}"></i>
                </div>
            </div>
        </div>
        
        <!-- Refraksi -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Refraksi</p>
                    <p class="text-2xl font-bold {{ $refColor }}">
                        {{ number_format($todayMetrics['refraksi_persen'], 2) }}%
                    </p>
                </div>
                <div class="p-3 {{ $refBg }} rounded-full">
                    <i class="fas fa-exclamation-triangle {{ $refColor }}"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="mb-4">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 ring-1 ring-yellow-200 dark:ring-yellow-800">
                <i class="fas fa-calendar-alt"></i>
                <span class="text-sm font-semibold">{{ $summaryTitle ?? ('Ringkasan Bulan ' . date('F Y')) }}</span>
            </div>
            @if(!empty($selectedFilters['kebun']) || !empty($selectedFilters['divisi']))
            <div class="mt-3 flex flex-wrap gap-2">
                @if(!empty($selectedFilters['kebun']))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                        <i class="fas fa-tree mr-1"></i> Kebun: {{ $selectedFilters['kebun'] }}
                    </span>
                @endif
                @if(!empty($selectedFilters['divisi']))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-900 dark:text-fuchsia-200">
                        <i class="fas fa-layer-group mr-1"></i> Divisi: {{ $selectedFilters['divisi'] }}
                    </span>
                @endif
            </div>
            @endif
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">BJR Bulanan</p>
                <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($monthlyMetrics['bjr'], 2) }} </p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">AKP Bulanan</p>
                <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($monthlyMetrics['akp'] * 100, 2) }}%</p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Produksi</p>
                <p class="text-xl font-bold {{ $prodMonthlyColor }}">{{ number_format($monthlyMetrics['total_produksi'], 2) }} kg</p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">ACV Prod</p>
                <p class="text-xl font-bold {{ $acvMonthlyColor }}">{{ number_format($monthlyMetrics['acv_prod'], 2) }}%</p>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- PKS vs Budget 7 Hari Terakhir -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-chart-line mr-2"></i>
        PKS vs Budget (7 Hari Terakhir)
            </h3>
            <div class="h-64">
                <canvas id="dailyProductionChart"></canvas>
            </div>
        </div>
        
    <!-- Realisasi AKP (%) per Kebun (Bulan Terpilih) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-chart-pie mr-2"></i>
        Realisasi AKP (%) per Kebun (Bulan Ini/Terpilih)
            </h3>
            <div class="h-64">
                <canvas id="productionByKebunChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            <i class="fas fa-bolt mr-2"></i>
            Aksi Cepat
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('panen-harian.create') }}" 
               class="flex items-center p-4 bg-green-50 dark:bg-green-900 hover:bg-green-100 dark:hover:bg-green-800 rounded-lg transition-colors duration-200 group">
                <div class="p-3 bg-green-500 rounded-full mr-4">
                    <i class="fas fa-plus text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-green-700 dark:text-green-200">Input Panen Harian</p>
                    <p class="text-sm text-green-600 dark:text-green-300">Tambah data panen hari ini</p>
                </div>
            </a>
            
            <a href="{{ route('panen-harian.index') }}" 
               class="flex items-center p-4 bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 rounded-lg transition-colors duration-200 group">
                <div class="p-3 bg-blue-500 rounded-full mr-4">
                    <i class="fas fa-table text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-blue-700 dark:text-blue-200">Report Harian</p>
                    <p class="text-sm text-blue-600 dark:text-blue-300">Lihat data panen harian</p>
                </div>
            </a>
            
            <a href="{{ route('panen-bulanan.index') }}" 
               class="flex items-center p-4 bg-purple-50 dark:bg-purple-900 hover:bg-purple-100 dark:hover:bg-purple-800 rounded-lg transition-colors duration-200 group">
                <div class="p-3 bg-purple-500 rounded-full mr-4">
                    <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-purple-700 dark:text-purple-200">Report Bulanan</p>
                    <p class="text-sm text-purple-600 dark:text-purple-300">Lihat data panen bulanan</p>
                </div>
            </a>
            
            <a href="{{ route('panen-harian.export') }}" 
               class="flex items-center p-4 bg-orange-50 dark:bg-orange-900 hover:bg-orange-100 dark:hover:bg-orange-800 rounded-lg transition-colors duration-200 group">
                <div class="p-3 bg-orange-500 rounded-full mr-4">
                    <i class="fas fa-download text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-orange-700 dark:text-orange-200">Export Data</p>
                    <p class="text-sm text-orange-600 dark:text-orange-300">Download laporan Excel</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Production Chart
    const dailyCtx = document.getElementById('dailyProductionChart').getContext('2d');
    const dailyData = @json($chartData['daily_pks_budget']);
    
    window.__charts = window.__charts || [];
    const dailyChart = new Chart(dailyCtx, {
        data: {
            labels: dailyData.map(item => {
                const date = new Date(item.tanggal_panen);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            }),
            datasets: [
                {
                    type: 'bar',
                    label: 'PKS (kg)',
                    data: dailyData.map(item => item.total_pks),
                    backgroundColor: 'rgba(34, 197, 94, 0.6)'
                },
                {
                    type: 'line',
                    label: 'Budget (kg)',
                    data: dailyData.map(item => item.total_budget),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    tension: 0.3,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('id-ID').format(value) + ' kg';
                        }
                    }
                }
            }
        }
    });
    window.__charts.push(dailyChart);
    
    // Production by Kebun Chart
    const kebunCtx = document.getElementById('productionByKebunChart').getContext('2d');
    const akpDaily = @json($chartData['akp_daily']);

    const kebunChart = new Chart(kebunCtx, {
        type: 'line',
        data: {
            labels: akpDaily.map(item => {
                const date = new Date(item.tanggal_panen);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            }),
            datasets: [{
                label: 'AKP (%) Harian',
                data: akpDaily.map(item => item.akp_pct),
                borderColor: 'rgb(245, 158, 11)',
                backgroundColor: 'rgba(245, 158, 11, 0.2)',
                tension: 0.3,
                fill: true,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return value + '%'; }
                    }
                }
            }
        }
    });
    window.__charts.push(kebunChart);
});
</script>
@endpush

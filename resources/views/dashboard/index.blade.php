@extends('layouts.app')

@section('title', 'Dashboard - PT Sahabat Agro Group')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h2>
                <p class="text-green-100">PT Sahabat Agro Group - Sistem Report Panen Sawit Digital - {{ date('d F Y') }}</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-seedling text-6xl text-green-200"></i>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="kebun" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kebun</label>
                <select name="kebun" id="kebun" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Semua Kebun</option>
                    @foreach($kebunList as $kebun)
                        <option value="{{ $kebun->id }}" {{ request('kebun') == $kebun->id ? 'selected' : '' }}>
                            {{ $kebun->nama_kebun }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="divisi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Divisi</label>
                <select name="divisi" id="divisi" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Semua Divisi</option>
                    @foreach($divisiList as $divisi)
                        <option value="{{ $divisi->id }}" {{ request('divisi') == $divisi->id ? 'selected' : '' }}>
                            {{ $divisi->nama_divisi }}
                        </option>
                    @endforeach
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
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ number_format($todayMetrics['total_tk']) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tenaga Kerja Panen</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <i class="fas fa-users text-purple-600 dark:text-purple-300"></i>
                </div>
            </div>
        </div>
        
        <!-- ACV Prod Harian -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">ACV Prod Harian</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ number_format($todayMetrics['acv_prod'], 2) }}%
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Achievement vs Budget</p>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                    <i class="fas fa-percentage text-orange-600 dark:text-orange-300"></i>
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
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ number_format($todayMetrics['total_produksi'], 2) }} kg
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <i class="fas fa-seedling text-green-600 dark:text-green-300"></i>
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
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ number_format($todayMetrics['refraksi_persen'], 2) }}%
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                    <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-300"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            <i class="fas fa-calendar-alt mr-2"></i>
            Ringkasan Bulan {{ date('F Y') }}
        </h3>
        
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
                <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($monthlyMetrics['total_produksi'], 2) }} kg</p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">ACV Prod</p>
                <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($monthlyMetrics['acv_prod'], 2) }}%</p>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daily Production Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-chart-line mr-2"></i>
                Produksi 7 Hari Terakhir
            </h3>
            <div class="h-64">
                <canvas id="dailyProductionChart"></canvas>
            </div>
        </div>
        
        <!-- Production by Kebun Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-chart-pie mr-2"></i>
                Produksi per Kebun (Bulan Ini)
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
    const dailyData = @json($chartData['daily_production']);
    
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(item => {
                const date = new Date(item.tanggal);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            }),
            datasets: [{
                label: 'Produksi (kg)',
                data: dailyData.map(item => item.total_produksi),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
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
    
    // Production by Kebun Chart
    const kebunCtx = document.getElementById('productionByKebunChart').getContext('2d');
    const kebunData = @json($chartData['production_by_kebun']);
    
    new Chart(kebunCtx, {
        type: 'doughnut',
        data: {
            labels: kebunData.map(item => item.kebun),
            datasets: [{
                data: kebunData.map(item => item.total_produksi),
                backgroundColor: [
                    'rgb(34, 197, 94)',
                    'rgb(59, 130, 246)',
                    'rgb(168, 85, 247)',
                    'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush

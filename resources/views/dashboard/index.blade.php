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
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border">
            <h3 class="font-semibold mb-2">Ringkasan Hari Ini</h3>
            <ul class="text-sm space-y-1">
                <li>BJR: {{ number_format($todayMetrics['bjr'] ?? 0, 2) }}</li>
                <li>AKP: {{ number_format(($todayMetrics['akp'] ?? 0) * 100, 2) }}%</li>
                <li>HK: {{ number_format($todayMetrics['total_tk'] ?? 0) }}</li>
                <li>ACV Prod: {{ number_format($todayMetrics['acv_prod'] ?? 0, 2) }}%</li>
            </ul>
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border">
            <h3 class="font-semibold mb-2">Ringkasan Bulan</h3>
            <ul class="text-sm space-y-1">
                <li>BJR: {{ number_format($monthlyMetrics['bjr'] ?? 0, 2) }}</li>
                <li>AKP: {{ number_format(($monthlyMetrics['akp'] ?? 0) * 100, 2) }}%</li>
                <li>Total Produksi PKS: {{ number_format($monthlyMetrics['total_produksi'] ?? 0, 2) }} kg</li>
                <li>ACV Prod: {{ number_format($monthlyMetrics['acv_prod'] ?? 0, 2) }}%</li>
            </ul>
        </div>
    </div>

    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border">
        <p class="text-sm text-gray-600 dark:text-gray-300">Tampilan disederhanakan sementara untuk menghilangkan error 500. Grafik dan filter akan diaktifkan kembali setelah konfirmasi halaman ini sudah 200 OK.</p>
    </div>
</div>
@endsection

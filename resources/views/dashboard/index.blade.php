@extends('layouts.app')

@section('title', 'Dashboard - PT Sahabat Agro Group')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Selamat Datang, {{ $userName ?? 'User' }}!</h2>
        <p class="text-green-100 mt-1">PT Sahabat Agro Group — {{ date('d F Y') }}</p>
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

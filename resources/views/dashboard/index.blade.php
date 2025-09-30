@extends('layouts.app')

@section('title', 'Dashboard - PT Sahabat Agro Group')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Selamat Datang, {{ $userName ?? 'User' }}!</h2>
        <p class="text-white/80 mt-1">PT Sahabat Agro Group — {{ date('d F Y') }}</p>
    </div>

    <!-- Filter Section (safe) -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="kebun" class="block text-sm font-medium text-gray-900 mb-1">Kebun</label>
                <select name="kebun" id="kebun" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Semua Kebun</option>
                    @foreach(($kebunList ?? []) as $k)
                        <option value="{{ $k }}" {{ request('kebun') == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="divisi" class="block text-sm font-medium text-gray-900 mb-1">Divisi</label>
                <select name="divisi" id="divisi" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Semua Divisi</option>
                    @foreach(($divisiList ?? []) as $d)
                        <option value="{{ $d }}" {{ request('divisi') == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Bulan</label>
                <select name="bulan" id="bulan" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Bulan Ini</option>
                    @foreach(($bulanList ?? []) as $b)
                        <option value="{{ strtoupper($b) }}" {{ strtoupper((string)request('bulan')) === strtoupper((string)$b) ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Tahun</label>
                <select name="tahun" id="tahun" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Tahun Ini</option>
                    @for($y = ($yearNow ?? (int)date('Y'))+1; $y >= ($yearNow ?? (int)date('Y'))-5; $y--)
                        <option value="{{ $y }}" {{ (string)request('tahun') === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    <i class="fas fa-filter mr-2"></i>
                    Filter Data
                </button>
            </div>
        </form>
    </div>

    <!-- Filter Chips & Summary Title -->
    <div class="flex flex-col gap-3">
    <div class="flex flex-wrap items-center gap-2">
            @php
                $hasAny = !empty($selectedFilters['kebun']) || !empty($selectedFilters['divisi']) || !empty($selectedFilters['bulan']) || !empty($selectedFilters['tahun']);
            @endphp
            @if($hasAny)
        <span class="text-sm text-gray-600 mr-1">Filter aktif:</span>
                @if(!empty($selectedFilters['kebun']))
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">Kebun: {{ $selectedFilters['kebun'] }}</span>
                @endif
                @if(!empty($selectedFilters['divisi']))
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">Divisi: {{ $selectedFilters['divisi'] }}</span>
                @endif
                @if(!empty($selectedFilters['bulan']))
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">Bulan: {{ ucfirst(strtolower($selectedFilters['bulan'])) }}</span>
                @endif
                @if(!empty($selectedFilters['tahun']))
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">Tahun: {{ $selectedFilters['tahun'] }}</span>
                @endif
        <a href="{{ route('dashboard') }}" class="ml-2 text-sm px-3 py-1 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Reset Filter</a>
            @endif
        </div>
        @if(!empty($summaryTitle))
        <h3 class="text-lg font-semibold text-gray-900">{{ $summaryTitle }}</h3>
        @endif
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-1">Produksi PKS (Bulan)</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['total_produksi'] ?? 0, 2) }} <span class="text-sm font-medium">kg</span></p>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-1">Refraksi</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['refraksi_kg'] ?? 0, 2) }} <span class="text-sm">kg</span> <span class="text-gray-600 text-base">• {{ number_format($monthlyMetrics['refraksi_persen'] ?? 0, 2) }}%</span></p>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-1">Restan</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['restan_jjg'] ?? 0, 2) }} <span class="text-sm">JJG</span> <span class="text-gray-600 text-base">• {{ number_format($monthlyMetrics['restan_persen'] ?? 0, 2) }}%</span></p>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-1">JJG / PKK (Bulan)</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['jjg_per_pkk'] ?? 0, 2) }}</p>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-1">Ha / HK (Bulan)</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['ha_per_hk'] ?? 0, 2) }}</p>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-1">Ton / HK (Bulan)</h3>
            <p class="text-2xl font-bold">{{ number_format($monthlyMetrics['ton_per_hk'] ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Quick Stats Chips (Hari Ini) -->
    <div class="bg-white rounded-xl p-4 border border-gray-200">
        <h4 class="text-sm font-semibold text-gray-900 mb-2">Hari Ini</h4>
        <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-600 text-xs">ACV {{ number_format($todayMetrics['acv_prod'] ?? 0, 2) }}%</span>
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-600 text-xs">AKP {{ number_format(($todayMetrics['akp'] ?? 0) * 100, 2) }}%</span>
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-600 text-xs">BJR {{ number_format($todayMetrics['bjr'] ?? 0, 2) }}</span>
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-600 text-xs">Restan {{ number_format($todayMetrics['restan_persen'] ?? 0, 2) }}%</span>
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">Refraksi {{ number_format($todayMetrics['refraksi_persen'] ?? 0, 2) }}%</span>
        </div>
    </div>

    
    <!-- Compact KPI row (ringkas dan konsisten warna) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">ACV Produksi (Bulan)</div>
            <div class="mt-1 text-2xl font-semibold text-green-600">{{ number_format($monthlyMetrics['acv_prod'] ?? 0, 2) }}%</div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">AKP (Bulan)</div>
            <div class="mt-1 text-2xl font-semibold text-blue-600">{{ number_format(($monthlyMetrics['akp'] ?? 0) * 100, 2) }}%</div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">BJR (Bulan)</div>
            <div class="mt-1 text-2xl font-semibold text-amber-600">{{ number_format($monthlyMetrics['bjr'] ?? 0, 2) }}</div>
        </div>
    </div>
</div>
@endsection

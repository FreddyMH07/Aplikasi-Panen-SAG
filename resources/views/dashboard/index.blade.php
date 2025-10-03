@extends('layouts.app')

@section('title', 'Dashboard - PT Sahabat Agro Group')
@section('page-title', 'Dashboard')

@php
    $initialActiveFilters = [
        'Kebun' => request('kebun'),
        'Divisi' => request('divisi'),
        'Bulan' => request('bulan'),
        'Tahun' => request('tahun'),
    ];
    $initialActiveFilters = array_filter($initialActiveFilters, fn ($value) => filled($value));
    $nowJakarta = \Carbon\Carbon::now()->timezone('Asia/Jakarta');
    $jsInitialFilters = [
        'kebun' => request('kebun'),
        'divisi' => request('divisi'),
        'bulan' => request('bulan'),
        'tahun' => request('tahun'),
    ];
    $jsChartData = [
        'daily_pks_budget' => $chartData['daily_pks_budget'] ?? [],
        'akp_daily' => $chartData['akp_daily'] ?? [],
    ];
@endphp

@section('content')
<div class="space-y-8">
    <!-- Hero banner with agriculture theme -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#15803d] via-[#16a34a] to-[#22c55e] p-8 text-white shadow-xl">
        <!-- Agricultural pattern overlay -->
        <div class="absolute inset-0 opacity-[0.03]">
            <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="palm-pattern" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
                        <circle cx="20" cy="20" r="2" fill="white"/>
                        <circle cx="60" cy="60" r="2" fill="white"/>
                        <path d="M40 30 Q35 35 40 40 Q45 35 40 30 M38 35 L42 35 M40 33 L40 37" stroke="white" fill="none" stroke-width="1.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#palm-pattern)"/>
            </svg>
        </div>
        <!-- Decorative palm fronds -->
        <div class="absolute right-0 top-0 h-full w-1/3 opacity-5">
            <svg viewBox="0 0 200 200" class="h-full w-full">
                <path d="M100 100 Q120 80 140 70 M100 100 Q130 90 150 85 M100 100 Q125 105 145 110" stroke="white" fill="none" stroke-width="3" stroke-linecap="round"/>
                <path d="M100 100 Q80 80 60 70 M100 100 Q70 90 50 85 M100 100 Q75 105 55 110" stroke="white" fill="none" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-sm">
                    <i class="fas fa-seedling text-3xl text-white"></i>
                </div>
                <div>
                    <p class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-white/90">
                        <i class="fas fa-chart-line"></i>
                        Dashboard Operasional Panen Sawit
                    </p>
                    <h2 class="mt-2 text-3xl font-bold drop-shadow-lg">Selamat Datang, {{ $userName ?? 'User' }}</h2>
                    <p class="mt-2 flex items-center gap-2 text-white/95 drop-shadow-md">
                        <i class="fas fa-building"></i>
                        PT Sahabat Agro Group — {{ $todayFormatted ?? date('d F Y') }}
                    </p>
                </div>
            </div>
            <div class="flex flex-col gap-4 text-base">
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm">
                        <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-emerald-300 shadow-lg shadow-emerald-400/50"></span>
                        Data Real-Time
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm text-white backdrop-blur-sm">
                        <i class="fas fa-clock"></i>
                        {{ $nowJakarta->format('d M Y, H:i') }} WIB
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm text-white backdrop-blur-sm">
                        <i class="fas fa-cloud-sun"></i>
                        Musim Panen
                    </span>
                </div>
                <div id="activeFilterChips" class="flex flex-wrap gap-2">
                    @forelse($initialActiveFilters as $label => $value)
                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-medium">{{ $label }}: {{ $value }}</span>
                    @empty
                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-medium">Semua data ditampilkan</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="grid items-start gap-6 xl:grid-cols-[320px_minmax(0,1fr)]">
        <!-- Sidebar: Filters & notes -->
        <aside class="space-y-6">
            <div class="rounded-2xl border-2 border-green-100 bg-gradient-to-br from-white to-green-50/30 p-6 shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100">
                                <i class="fas fa-filter text-sm text-green-700"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Filter Analitik</h3>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">Atur rentang data kebun & periode panen</p>
                    </div>
                    <span id="filterStatusBadge" class="inline-flex items-center rounded-full bg-green-100 px-3 py-1.5 text-xs font-semibold text-green-800 shadow-sm transition">Siap</span>
                </div>
                <form id="dashboardFilterForm" action="{{ route('dashboard') }}" method="GET" class="mt-6 space-y-4">
                    <div class="space-y-2">
                        <label for="kebun" class="flex items-center gap-2 text-sm font-semibold text-gray-800">
                            <i class="fas fa-leaf text-green-600"></i>
                            Kebun Sawit
                        </label>
                        <div class="relative">
                            <i class="fas fa-tree pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-green-500"></i>
                            <select name="kebun" id="kebun" class="block w-full rounded-lg border-2 border-gray-200 bg-white py-2.5 pl-10 pr-3 text-sm font-medium text-gray-900 shadow-sm transition hover:border-green-300 focus:border-green-500 focus:ring-2 focus:ring-green-200">
                                <option value="">Semua Kebun</option>
                                @foreach(($kebunList ?? []) as $k)
                                    <option value="{{ $k }}" {{ request('kebun') == $k ? 'selected' : '' }}>{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="divisi" class="flex items-center gap-2 text-sm font-semibold text-gray-800">
                            <i class="fas fa-sitemap text-green-600"></i>
                            Divisi Operasional
                        </label>
                        <div class="relative">
                            <i class="fas fa-layer-group pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-green-500"></i>
                            <select name="divisi" id="divisi" class="block w-full rounded-lg border-2 border-gray-200 bg-white py-2.5 pl-10 pr-3 text-sm font-medium text-gray-900 shadow-sm transition hover:border-green-300 focus:border-green-500 focus:ring-2 focus:ring-green-200">
                                <option value="">Semua Divisi</option>
                                @foreach(($divisiList ?? []) as $d)
                                    <option value="{{ $d }}" {{ request('divisi') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="bulan" class="flex items-center gap-2 text-sm font-semibold text-gray-800">
                            <i class="fas fa-calendar-alt text-green-600"></i>
                            Periode Bulan
                        </label>
                        <div class="relative">
                            <i class="fas fa-calendar-days pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-green-500"></i>
                            <select name="bulan" id="bulan" class="block w-full rounded-lg border-2 border-gray-200 bg-white py-2.5 pl-10 pr-3 text-sm font-medium text-gray-900 shadow-sm transition hover:border-green-300 focus:border-green-500 focus:ring-2 focus:ring-green-200">
                                <option value="">Bulan Ini</option>
                                @foreach(($bulanList ?? []) as $b)
                                    <option value="{{ strtoupper($b) }}" {{ strtoupper((string)request('bulan')) === strtoupper((string)$b) ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="tahun" class="flex items-center gap-2 text-sm font-semibold text-gray-800">
                            <i class="fas fa-calendar-check text-green-600"></i>
                            Tahun Panen
                        </label>
                        <div class="relative">
                            <i class="fas fa-calendar pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-green-500"></i>
                            <select name="tahun" id="tahun" class="block w-full rounded-lg border-2 border-gray-200 bg-white py-2.5 pl-10 pr-3 text-sm font-medium text-gray-900 shadow-sm transition hover:border-green-300 focus:border-green-500 focus:ring-2 focus:ring-green-200">
                                <option value="">Tahun Ini</option>
                                @for($y = ($yearNow ?? (int)date('Y'))+1; $y >= ($yearNow ?? (int)date('Y'))-5; $y--)
                                    <option value="{{ $y }}" {{ (string)request('tahun') === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" id="applyFilters" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-green-700 to-green-800 px-4 py-3 text-sm font-bold text-white shadow-lg transition hover:from-green-800 hover:to-green-900 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-green-300">
                            <i class="fas fa-search"></i>
                            Tampilkan Data
                        </button>
                        <button type="button" id="resetFilters" class="inline-flex items-center justify-center gap-2 rounded-lg border-2 border-gray-400 bg-white px-4 py-3 text-sm font-bold text-gray-800 shadow-sm transition hover:border-gray-500 hover:bg-gray-100 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-gray-200">
                            <i class="fas fa-redo"></i>
                            Reset
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-info-circle text-green-600"></i>
                        Klik "Tampilkan Data" untuk memperbarui analitik panen
                    </p>
                </form>
            </div>

            <div class="rounded-2xl border-2 border-amber-100 bg-gradient-to-br from-amber-50/50 to-white p-6 shadow-md">
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100">
                        <i class="fas fa-lightbulb text-sm text-amber-700"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Catatan Panen</h3>
                </div>
                <ul class="space-y-3 text-sm text-gray-700">
                    <li class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-green-100">
                            <i class="fas fa-database text-xs text-green-700"></i>
                        </div>
                        <span>Data harian terintegrasi dari sistem panen sawit PT SAG setiap pukul 05.00 WIB.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-blue-100">
                            <i class="fas fa-sync text-xs text-blue-700"></i>
                        </div>
                        <span>Pembaharuan filter memicu refresh real-time pada KPI, ringkasan bulanan, dan grafik produksi TBS.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-amber-100">
                            <i class="fas fa-chart-bar text-xs text-amber-700"></i>
                        </div>
                        <span>Warna KPI menunjukkan status capaian terhadap target operasional perkebunan sawit.</span>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main analytic content -->
        <section class="space-y-8">
            <!-- Daily KPIs -->
            @php
                $acv = (float)($todayMetrics['acv_prod'] ?? 0);
                $acvColor = $acv < 70 ? 'text-[#DC2626]' : ($acv < 85 ? 'text-[#F59E0B]' : ($acv <= 110 ? 'text-[#16A34A]' : 'text-[#2563EB]'));
            @endphp
            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-green-100 to-green-200">
                            <i class="fas fa-chart-line text-xl text-green-700"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Monitoring Produksi Harian</h3>
                            <p class="text-sm text-gray-600">Ikhtisar kinerja panen & kualitas TBS hari ini</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-green-100 px-4 py-2 text-xs font-bold text-green-800 shadow-sm">
                        <i class="fas fa-clock"></i>
                        Update Harian
                    </span>
                </div>
                <div id="kpiTodayWrap" class="relative">
                    <div id="kpiTodayLoading" class="hidden absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/70 backdrop-blur-[1px]">
                        <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <article class="group relative overflow-hidden rounded-2xl border-2 border-amber-100 bg-gradient-to-br from-white to-amber-50/30 p-6 shadow-md transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-xl">
                            <div class="absolute right-0 top-0 h-full w-24 opacity-5">
                                <i class="fas fa-apple-whole text-[80px] text-amber-600"></i>
                            </div>
                            <div class="relative flex items-center gap-3">
                                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-amber-200 text-amber-700 shadow-sm">
                                    <i class="fas fa-weight-hanging text-lg"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">BJR Hari Ini</p>
                                    <p class="text-xs text-gray-500">Berat rata-rata TBS</p>
                                </div>
                            </div>
                            <div class="relative mt-6 flex items-end justify-between">
                                <p class="text-4xl font-bold text-amber-600" data-metric-value="today.bjr">{{ number_format($todayMetrics['bjr'] ?? 0, 2) }}</p>
                                <span class="text-sm font-semibold text-gray-500">kg/jjg</span>
                            </div>
                        </article>
                        <article class="group relative overflow-hidden rounded-2xl border-2 border-blue-100 bg-gradient-to-br from-white to-blue-50/30 p-6 shadow-md transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl">
                            <div class="absolute right-0 top-0 h-full w-24 opacity-5">
                                <i class="fas fa-bullseye text-[80px] text-blue-600"></i>
                            </div>
                            <div class="relative flex items-center gap-3">
                                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700 shadow-sm">
                                    <i class="fas fa-chart-pie text-lg"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">AKP Hari Ini</p>
                                    <p class="text-xs text-gray-500">Kerapatan panen</p>
                                </div>
                            </div>
                            <div class="relative mt-6 flex items-end justify-between">
                                <p class="text-4xl font-bold text-blue-600" data-metric-value="today.akp_pct">{{ number_format(($todayMetrics['akp'] ?? 0) * 100, 2) }}%</p>
                                <span class="text-sm font-semibold text-gray-500">%</span>
                            </div>
                        </article>
                        <article class="group relative overflow-hidden rounded-2xl border-2 border-cyan-100 bg-gradient-to-br from-white to-cyan-50/30 p-6 shadow-md transition hover:-translate-y-1 hover:border-cyan-200 hover:shadow-xl">
                            <div class="absolute right-0 top-0 h-full w-24 opacity-5">
                                <i class="fas fa-users text-[80px] text-cyan-600"></i>
                            </div>
                            <div class="relative flex items-center gap-3">
                                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-100 to-cyan-200 text-cyan-700 shadow-sm">
                                    <i class="fas fa-hard-hat text-lg"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">HK Hari Ini</p>
                                    <p class="text-xs text-gray-500">Tenaga kerja panen</p>
                                </div>
                            </div>
                            <div class="relative mt-6 flex items-end justify-between">
                                <p class="text-4xl font-bold text-cyan-600" data-metric-value="today.hk">{{ number_format($todayMetrics['total_tk'] ?? 0) }}</p>
                                <span class="text-sm font-semibold text-gray-500">orang</span>
                            </div>
                        </article>
                        <article class="group relative overflow-hidden rounded-2xl border-2 border-green-200 bg-gradient-to-br from-white to-green-50/40 p-6 shadow-md transition hover:-translate-y-1 hover:border-green-300 hover:shadow-xl">
                            <div class="absolute right-0 top-0 h-full w-24 opacity-5">
                                <i class="fas fa-trophy text-[80px] text-green-600"></i>
                            </div>
                            <div class="relative flex items-center gap-3">
                                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-green-100 to-green-200 text-green-700 shadow-sm">
                                    <i class="fas fa-chart-line text-lg"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">ACV Produksi</p>
                                    <p class="text-xs text-gray-500">Target vs realisasi</p>
                                </div>
                            </div>
                            <div class="relative mt-6 flex items-end justify-between">
                                <p class="text-4xl font-bold {{ $acvColor }}" data-metric-value="today.acv_prod" data-threshold="acv">{{ number_format($acv, 2) }}%</p>
                                <span class="text-sm font-semibold text-gray-500">%</span>
                            </div>
                        </article>
                    </div>
                </div>
            </div>

            <!-- Secondary KPIs -->
            @php
                $sel = (float)($todayMetrics['selisih'] ?? 0);
                $selColor = $sel >= 0 ? 'text-[#16A34A]' : 'text-[#DC2626]';
                $refPct = (float)($todayMetrics['refraksi_persen'] ?? 0);
                $refColor = $refPct <= 1 ? 'text-[#16A34A]' : ($refPct <= 2 ? 'text-[#F59E0B]' : 'text-[#DC2626]');
            @endphp
            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-100 to-blue-200">
                            <i class="fas fa-balance-scale text-xl text-blue-700"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Kinerja Timbang & Kualitas TBS</h3>
                            <p class="text-sm text-gray-600">Produksi, selisih timbang, dan refraksi buah sawit</p>
                        </div>
                    </div>
                </div>
                <div id="kpiSecondaryWrap" class="relative">
                    <div id="kpiSecondaryLoading" class="hidden absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/70 backdrop-blur-[1px]">
                        <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <article class="group relative overflow-hidden rounded-2xl border-2 border-green-100 bg-gradient-to-br from-white to-green-50/30 p-6 shadow-md transition hover:-translate-y-1 hover:border-green-200 hover:shadow-xl">
                            <div class="absolute right-0 top-0 h-full w-20 opacity-5">
                                <i class="fas fa-truck-loading text-[60px] text-green-600"></i>
                            </div>
                            <div class="relative flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-green-100 to-green-200 text-green-700">
                                        <i class="fas fa-box text-base"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">Total Produksi</p>
                                        <p class="text-xs text-gray-500">Realisasi TBS hari ini</p>
                                    </div>
                                </div>
                                <span class="rounded-lg bg-green-100 px-3 py-1 text-xs font-bold text-green-800">Volume</span>
                            </div>
                            <p class="relative mt-6 text-3xl font-bold text-green-700" data-metric-value="today.total_produksi">{{ number_format($todayMetrics['total_produksi'] ?? 0, 2) }} <span class="text-lg text-gray-500">kg</span></p>
                        </article>
                        <article class="group relative overflow-hidden rounded-2xl border-2 border-blue-100 bg-gradient-to-br from-white to-blue-50/30 p-6 shadow-md transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl">
                            <div class="absolute right-0 top-0 h-full w-20 opacity-5">
                                <i class="fas fa-exchange-alt text-[60px] text-blue-600"></i>
                            </div>
                            <div class="relative flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700">
                                        <i class="fas fa-balance-scale-right text-base"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">Selisih Timbang</p>
                                        <p class="text-xs text-gray-500">Kebun vs PKS</p>
                                    </div>
                                </div>
                                <span class="rounded-lg bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">Audit</span>
                            </div>
                            <div class="relative mt-6 flex items-end justify-between">
                                <p class="text-3xl font-bold {{ $selColor }}" data-metric-value="today.selisih" data-threshold="diff">{{ number_format($sel ?? 0, 2) }} <span class="text-lg text-gray-500">kg</span></p>
                                <div class="text-right text-sm">
                                    <span class="block text-xs font-semibold text-gray-600">Persentase</span>
                                    <div class="text-lg font-bold {{ $selColor }}"><span data-metric-value="today.selisih_percent">{{ number_format($todayMetrics['selisih_persen'] ?? 0, 2) }}</span>%</div>
                                </div>
                            </div>
                        </article>
                        <article class="group relative overflow-hidden rounded-2xl border-2 border-amber-100 bg-gradient-to-br from-white to-amber-50/30 p-6 shadow-md transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-xl">
                            <div class="absolute right-0 top-0 h-full w-20 opacity-5">
                                <i class="fas fa-search text-[60px] text-amber-600"></i>
                            </div>
                            <div class="relative flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-amber-100 to-amber-200 text-amber-700">
                                        <i class="fas fa-clipboard-check text-base"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">Refraksi TBS</p>
                                        <p class="text-xs text-gray-500">Potongan kualitas</p>
                                    </div>
                                </div>
                                <span class="rounded-lg bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">QC</span>
                            </div>
                            <div class="relative mt-6 space-y-2">
                                <p class="text-3xl font-bold {{ $refColor }}" data-metric-value="today.refraksi_percent" data-threshold="refraksi">{{ number_format($todayMetrics['refraksi_persen'] ?? 0, 2) }}%</p>
                                <p class="text-sm font-semibold text-gray-600"><span data-metric-value="today.refraksi_kg">{{ number_format($todayMetrics['refraksi_kg'] ?? 0, 2) }}</span> kg dipotong</p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>

            <!-- Monthly summary -->
            @php
                $summaryTitle = $summaryTitle ?? 'Ringkasan Produksi Bulan Berjalan';
                $macv = (float)($monthlyMetrics['acv_prod'] ?? 0);
                $macvColor = $macv < 70 ? 'text-[#DC2626]' : ($macv < 85 ? 'text-[#F59E0B]' : ($macv <= 110 ? 'text-[#16A34A]' : 'text-[#2563EB]'));
                $mref = (float)($monthlyMetrics['refraksi_persen'] ?? 0);
                $mrefColor = $mref <= 1 ? 'text-[#16A34A]' : ($mref <= 2 ? 'text-[#F59E0B]' : 'text-[#DC2626]');
            @endphp
            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200">
                            <i class="fas fa-calendar-check text-xl text-emerald-700"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Ringkasan Produksi Bulanan</h3>
                            <p id="monthlySummaryTitle" class="text-sm text-gray-600" data-metric-value="summary.title">{{ $summaryTitle }}</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-4 py-2 text-xs font-bold text-emerald-800 shadow-sm">
                        <i class="fas fa-chart-bar"></i>
                        Agregasi Bulanan
                    </span>
                </div>
                <div id="kpiMonthlyWrap" class="relative">
                    <div id="kpiMonthlyLoading" class="hidden absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/70 backdrop-blur-[1px]">
                        <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
                    </div>
                    <div class="space-y-5">
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            <article class="rounded-2xl border-2 border-amber-100 bg-gradient-to-br from-white to-amber-50/20 p-5 shadow-md transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                                        <i class="fas fa-weight text-sm"></i>
                                    </span>
                                    <p class="text-sm font-bold text-gray-800">BJR Bulanan</p>
                                </div>
                                <p class="text-3xl font-bold text-amber-600" data-metric-value="monthly.bjr">{{ number_format($monthlyMetrics['bjr'] ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-500 mt-1">Rata-rata berat tandan sawit</p>
                            </article>
                            <article class="rounded-2xl border-2 border-blue-100 bg-gradient-to-br from-white to-blue-50/20 p-5 shadow-md transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                                        <i class="fas fa-percentage text-sm"></i>
                                    </span>
                                    <p class="text-sm font-bold text-gray-800">AKP Bulanan</p>
                                </div>
                                <p class="text-3xl font-bold text-blue-600" data-metric-value="monthly.akp_pct">{{ number_format(($monthlyMetrics['akp'] ?? 0) * 100, 2) }}%</p>
                                <p class="text-xs text-gray-500 mt-1">Rata-rata kerapatan panen</p>
                            </article>
                            <article class="rounded-2xl border-2 border-green-100 bg-gradient-to-br from-white to-green-50/20 p-5 shadow-md transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 text-green-700">
                                        <i class="fas fa-warehouse text-sm"></i>
                                    </span>
                                    <p class="text-sm font-bold text-gray-800">Total PKS</p>
                                </div>
                                <p class="text-3xl font-bold text-green-600" data-metric-value="monthly.total_produksi">{{ number_format($monthlyMetrics['total_produksi'] ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-500 mt-1">Akumulasi volume TBS bulanan</p>
                            </article>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            <article class="rounded-2xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/20 p-5 shadow-md transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                                        <i class="fas fa-trophy text-sm"></i>
                                    </span>
                                    <p class="text-sm font-bold text-gray-800">ACV Produksi</p>
                                </div>
                                <p class="text-3xl font-bold {{ $macvColor }}" data-metric-value="monthly.acv_prod" data-threshold="acv">{{ number_format($macv, 2) }}%</p>
                                <p class="text-xs text-gray-500 mt-1">Realisasi vs target bulanan</p>
                            </article>
                            <article class="rounded-2xl border-2 border-amber-100 bg-gradient-to-br from-white to-amber-50/20 p-5 shadow-md transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                                        <i class="fas fa-clipboard-check text-sm"></i>
                                    </span>
                                    <p class="text-sm font-bold text-gray-800">Refraksi Bulanan</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-3xl font-bold {{ $mrefColor }}" data-metric-value="monthly.refraksi_percent" data-threshold="refraksi">{{ number_format($mref, 2) }}%</p>
                                    <p class="text-sm font-semibold text-gray-600"><span data-metric-value="monthly.refraksi_kg">{{ number_format($monthlyMetrics['refraksi_kg'] ?? 0, 2) }}</span> kg dipotong</p>
                                </div>
                            </article>
                            <article class="rounded-2xl border-2 border-red-100 bg-gradient-to-br from-white to-red-50/20 p-5 shadow-md transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-700">
                                        <i class="fas fa-exclamation-triangle text-sm"></i>
                                    </span>
                                    <p class="text-sm font-bold text-gray-800">Buah Restan</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-3xl font-bold text-red-600" data-metric-value="monthly.restan_jjg">{{ number_format($monthlyMetrics['restan_jjg'] ?? 0) }}</p>
                                    <p class="text-sm font-semibold text-gray-600"><span data-metric-value="monthly.restan_percent">{{ number_format($monthlyMetrics['restan_persen'] ?? 0, 2) }}</span>% TBS restan</p>
                                </div>
                            </article>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            <article class="rounded-2xl border-2 border-purple-100 bg-gradient-to-br from-white to-purple-50/20 p-5 shadow-md transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 text-purple-700">
                                        <i class="fas fa-user-tie text-sm"></i>
                                    </span>
                                    <p class="text-sm font-bold text-gray-800">JJG / PKK</p>
                                </div>
                                <p class="text-3xl font-bold text-purple-600" data-metric-value="monthly.jjg_per_pkk">{{ number_format($monthlyMetrics['jjg_per_pkk'] ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-500 mt-1">Total PKK: <span data-metric-value="monthly.total_pkk" class="font-semibold">{{ number_format($monthlyMetrics['total_pkk'] ?? 0) }}</span> pemanen</p>
                            </article>
                            <article class="rounded-2xl border-2 border-teal-100 bg-gradient-to-br from-white to-teal-50/20 p-5 shadow-md transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-100 text-teal-700">
                                        <i class="fas fa-map text-sm"></i>
                                    </span>
                                    <p class="text-sm font-bold text-gray-800">Ha / HK</p>
                                </div>
                                <p class="text-3xl font-bold text-teal-600" data-metric-value="monthly.ha_per_hk">{{ number_format($monthlyMetrics['ha_per_hk'] ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-500 mt-1">Efisiensi luas panen per pekerja</p>
                            </article>
                            <article class="rounded-2xl border-2 border-indigo-100 bg-gradient-to-br from-white to-indigo-50/20 p-5 shadow-md transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                                        <i class="fas fa-chart-area text-sm"></i>
                                    </span>
                                    <p class="text-sm font-bold text-gray-800">Ton / HK</p>
                                </div>
                                <p class="text-3xl font-bold text-indigo-600" data-metric-value="monthly.ton_per_hk">{{ number_format($monthlyMetrics['ton_per_hk'] ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-500 mt-1">Produktivitas tonase per HK</p>
                            </article>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-100 to-blue-200">
                            <i class="fas fa-chart-bar text-xl text-blue-700"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Visualisasi Produksi Sawit</h3>
                            <p class="text-sm text-gray-600">Grafik perbandingan PKS vs budget dan tren realisasi AKP</p>
                        </div>
                    </div>
                </div>
                <div class="grid gap-6 lg:grid-cols-2">
                    <div id="chartPksBudgetContainer" class="relative overflow-hidden rounded-2xl border-2 border-green-100 bg-gradient-to-br from-white to-green-50/20 p-6 shadow-lg">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-green-100 to-green-200 text-green-700">
                                    <i class="fas fa-warehouse text-base"></i>
                                </span>
                                <div>
                                    <h4 class="text-base font-bold text-gray-900">PKS vs Budget Harian</h4>
                                    <p class="text-xs text-gray-600">Monitoring output pabrik kelapa sawit</p>
                                </div>
                            </div>
                            <span class="rounded-lg bg-green-100 px-3 py-1.5 text-xs font-bold text-green-800 shadow-sm">
                                <i class="fas fa-chart-column"></i>
                                Produksi
                            </span>
                        </div>
                        <div class="relative mt-6 h-[320px]">
                            <div id="chartPksBudgetLoading" class="hidden absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/80 backdrop-blur-[1px]">
                                <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
                            </div>
                            <canvas id="chartPksBudget" class="h-full w-full"></canvas>
                        </div>
                    </div>
                    <div id="chartAkpDailyContainer" class="relative overflow-hidden rounded-2xl border-2 border-blue-100 bg-gradient-to-br from-white to-blue-50/20 p-6 shadow-lg">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700">
                                    <i class="fas fa-chart-line text-base"></i>
                                </span>
                                <div>
                                    <h4 class="text-base font-bold text-gray-900">Rasio AKP (%) Harian</h4>
                                    <p class="text-xs text-gray-600">Tren kualitas angka kerapatan panen</p>
                                </div>
                            </div>
                            <span class="rounded-lg bg-blue-100 px-3 py-1.5 text-xs font-bold text-blue-800 shadow-sm">
                                <i class="fas fa-quality"></i>
                                Kualitas
                            </span>
                        </div>
                        <div class="relative mt-6 h-[320px]">
                            <div id="chartAkpDailyLoading" class="hidden absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/80 backdrop-blur-[1px]">
                                <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
                            </div>
                            <canvas id="chartAkpDaily" class="h-full w-full"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const FILTER_IDS = ['kebun', 'divisi', 'bulan', 'tahun'];
    const filterLabels = { kebun: 'Kebun', divisi: 'Divisi', bulan: 'Bulan', tahun: 'Tahun' };
    const monthMap = {
        JANUARI: 1,
        FEBRUARI: 2,
        MARET: 3,
        APRIL: 4,
        MEI: 5,
        JUNI: 6,
        JULI: 7,
        AGUSTUS: 8,
        SEPTEMBER: 9,
        OKTOBER: 10,
        NOVEMBER: 11,
        DESEMBER: 12,
        JANUARY: 1,
        FEBRUARY: 2,
        MARCH: 3,
        APRIL: 4,
        MAY: 5,
        JUNE: 6,
        JULY: 7,
        AUGUST: 8,
        SEPTEMBER: 9,
        OCTOBER: 10,
        NOVEMBER: 11,
        DECEMBER: 12,
    };
    const monthLabelMap = {
        JANUARI: 'Januari',
        FEBRUARI: 'Februari',
        MARET: 'Maret',
        APRIL: 'April',
        MEI: 'Mei',
        JUNI: 'Juni',
        JULI: 'Juli',
        AGUSTUS: 'Agustus',
        SEPTEMBER: 'September',
        OKTOBER: 'Oktober',
        NOVEMBER: 'November',
        DESEMBER: 'Desember',
        JANUARY: 'Januari',
        FEBRUARY: 'Februari',
        MARCH: 'Maret',
        MAY: 'Mei',
        JUNE: 'Juni',
        JULY: 'Juli',
        AUGUST: 'Agustus',
        OCTOBER: 'Oktober',
        DECEMBER: 'Desember',
    };

    const initialFilters = @json($jsInitialFilters);
    const initialChartData = @json($jsChartData);

    const filterForm = document.getElementById('dashboardFilterForm');
    const applyButton = document.getElementById('applyFilters');
    const resetButton = document.getElementById('resetFilters');
    const activeFilterChips = document.getElementById('activeFilterChips');
    const filterStatusBadge = document.getElementById('filterStatusBadge');

    const charts = { pksBudget: null, akpDaily: null };
    let fetchAbortController = null;
    let lastSuccessfulFilters = { ...initialFilters };

    const metricConfig = {
        'today.bjr': {
            get: data => normalizeNumber(data.todayMetrics?.bjr),
            format: value => formatNumber(value, 2),
        },
        'today.akp_pct': {
            get: data => normalizeNumber(data.todayMetrics?.akp) * 100,
            format: value => `${formatNumber(value, 2)}%`,
        },
        'today.hk': {
            get: data => normalizeNumber(data.todayMetrics?.total_tk),
            format: value => formatInteger(value),
        },
        'today.acv_prod': {
            get: data => normalizeNumber(data.todayMetrics?.acv_prod),
            format: value => `${formatNumber(value, 2)}%`,
        },
        'today.total_produksi': {
            get: data => normalizeNumber(data.todayMetrics?.total_produksi),
            format: value => formatNumber(value, 2),
        },
        'today.selisih': {
            get: data => normalizeNumber(data.todayMetrics?.selisih),
            format: value => formatNumber(value, 2),
        },
        'today.selisih_percent': {
            get: data => normalizeNumber(data.todayMetrics?.selisih_persen),
            format: value => formatNumber(value, 2),
        },
        'today.refraksi_percent': {
            get: data => normalizeNumber(data.todayMetrics?.refraksi_persen),
            format: value => formatNumber(value, 2),
        },
        'today.refraksi_kg': {
            get: data => normalizeNumber(data.todayMetrics?.refraksi_kg),
            format: value => formatNumber(value, 2),
        },
        'monthly.bjr': {
            get: data => normalizeNumber(data.monthlyMetrics?.bjr),
            format: value => formatNumber(value, 2),
        },
        'monthly.akp_pct': {
            get: data => normalizeNumber(data.monthlyMetrics?.akp) * 100,
            format: value => `${formatNumber(value, 2)}%`,
        },
        'monthly.total_produksi': {
            get: data => normalizeNumber(data.monthlyMetrics?.total_produksi),
            format: value => formatNumber(value, 2),
        },
        'monthly.acv_prod': {
            get: data => normalizeNumber(data.monthlyMetrics?.acv_prod),
            format: value => `${formatNumber(value, 2)}%`,
        },
        'monthly.refraksi_percent': {
            get: data => normalizeNumber(data.monthlyMetrics?.refraksi_persen),
            format: value => formatNumber(value, 2),
        },
        'monthly.refraksi_kg': {
            get: data => normalizeNumber(data.monthlyMetrics?.refraksi_kg),
            format: value => formatNumber(value, 2),
        },
        'monthly.restan_jjg': {
            get: data => normalizeNumber(data.monthlyMetrics?.restan_jjg),
            format: value => formatInteger(value),
        },
        'monthly.restan_percent': {
            get: data => normalizeNumber(data.monthlyMetrics?.restan_persen),
            format: value => formatNumber(value, 2),
        },
        'monthly.jjg_per_pkk': {
            get: data => normalizeNumber(data.monthlyMetrics?.jjg_per_pkk),
            format: value => formatNumber(value, 2),
        },
        'monthly.total_pkk': {
            get: data => normalizeNumber(data.monthlyMetrics?.total_pkk),
            format: value => formatInteger(value),
        },
        'monthly.ha_per_hk': {
            get: data => normalizeNumber(data.monthlyMetrics?.ha_per_hk),
            format: value => formatNumber(value, 2),
        },
        'monthly.ton_per_hk': {
            get: data => normalizeNumber(data.monthlyMetrics?.ton_per_hk),
            format: value => formatNumber(value, 2),
        },
        'summary.title': {
            get: data => data.summaryTitle || '',
            format: value => value || 'Ringkasan Produksi Bulanan',
        },
    };

    renderActiveFilterChips(initialFilters);
    setFilterBadge('ready', initialFilters);
    updateCharts(initialChartData, initialFilters);

    wireFilterEvents();

    function wireFilterEvents() {
        if (applyButton) {
            applyButton.addEventListener('click', () => runDashboardUpdate());
        }
        if (resetButton) {
            resetButton.addEventListener('click', () => {
                if (!filterForm) return;
                filterForm.reset();
                FILTER_IDS.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
                runDashboardUpdate();
            });
        }
        if (filterForm) {
            filterForm.addEventListener('submit', (event) => {
                event.preventDefault();
                runDashboardUpdate();
            });
        }
    }

    function collectFilters() {
        const values = {};
        FILTER_IDS.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            const value = (el.value || '').trim();
            if (value !== '') {
                values[id] = value;
            }
        });
        return values;
    }

    async function runDashboardUpdate() {
        const filters = collectFilters();
        if (fetchAbortController) {
            fetchAbortController.abort();
        }
        fetchAbortController = new AbortController();
        toggleAllLoading(true);
        setFilterBadge('loading', filters);
        try {
            const params = new URLSearchParams(filters);
            const url = params.toString() ? `{{ route('dashboard.json') }}?${params.toString()}` : `{{ route('dashboard.json') }}`;
            const response = await fetch(url, {
                signal: fetchAbortController.signal,
                headers: { 'Accept': 'application/json' },
            });
            if (!response.ok) {
                throw new Error(`Gagal memuat data dashboard (status ${response.status})`);
            }
            const json = await response.json();
            applyDashboardData(json);
            updateCharts(json.chartData || {}, json.selectedFilters || filters);
            lastSuccessfulFilters = { ...(json.selectedFilters || filters) };
            renderActiveFilterChips(lastSuccessfulFilters);
            setFilterBadge('ready', lastSuccessfulFilters);
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Gagal memperbarui dashboard', error);
            }
        } finally {
            toggleAllLoading(false);
        }
    }

    function toggleAllLoading(state) {
        ['kpiToday', 'kpiSecondary', 'kpiMonthly', 'chartPksBudget', 'chartAkpDaily'].forEach(section => setLoading(section, state));
    }

    function setLoading(section, on) {
        const ids = {
            kpiToday: 'kpiTodayLoading',
            kpiSecondary: 'kpiSecondaryLoading',
            kpiMonthly: 'kpiMonthlyLoading',
            chartPksBudget: 'chartPksBudgetLoading',
            chartAkpDaily: 'chartAkpDailyLoading',
        };
        const el = document.getElementById(ids[section]);
        if (!el) return;
        el.classList.toggle('hidden', !on);
    }

    function renderActiveFilterChips(filters = {}) {
        if (!activeFilterChips) return;
        const entries = Object.entries(filters).filter(([_, value]) => value !== undefined && value !== null && `${value}`.trim() !== '');
        activeFilterChips.innerHTML = '';
        if (!entries.length) {
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-medium';
            chip.textContent = 'Semua data ditampilkan';
            activeFilterChips.appendChild(chip);
            return;
        }
        entries.forEach(([key, value]) => {
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-xs font-medium';
            chip.textContent = `${filterLabels[key] ?? key}: ${formatFilterValue(key, value)}`;
            activeFilterChips.appendChild(chip);
        });
    }

    function formatFilterValue(key, value) {
        if (!value) return '';
        if (key === 'bulan') {
            const upper = value.toString().toUpperCase();
            return monthLabelMap[upper] ?? value;
        }
        return value;
    }

    function setFilterBadge(state, filters = {}) {
        if (!filterStatusBadge) return;
        const baseClass = 'inline-flex items-center rounded-full px-3 py-1 text-xs font-medium transition';
        const activeCount = Object.values(filters).filter(value => value !== undefined && value !== null && `${value}`.trim() !== '').length;
        if (state === 'loading') {
            filterStatusBadge.textContent = 'Memuat data...';
            filterStatusBadge.className = `${baseClass} bg-amber-50 text-amber-700`;
            return;
        }
        if (activeCount > 0) {
            filterStatusBadge.textContent = 'Filter aktif';
            filterStatusBadge.className = `${baseClass} bg-blue-50 text-[#1d4ed8]`;
        } else {
            filterStatusBadge.textContent = 'Siap';
            filterStatusBadge.className = `${baseClass} bg-green-50 text-[#15803d]`;
        }
    }

    function applyDashboardData(data) {
        const rawValues = {};
        Object.entries(metricConfig).forEach(([key, config]) => {
            if (!config || typeof config.get !== 'function') return;
            const raw = config.get(data);
            rawValues[key] = raw;
            const el = document.querySelector(`[data-metric-value="${key}"]`);
            if (!el) return;
            const formatted = config.format ? config.format(raw, data) : (raw ?? '');
            el.textContent = formatted;
        });
        applyThresholdColors(rawValues);
    }

    function applyThresholdColors(values) {
        const thresholdClassMap = {
            acv: ['text-[#DC2626]', 'text-[#F59E0B]', 'text-[#16A34A]', 'text-[#2563EB]'],
            refraksi: ['text-[#16A34A]', 'text-[#F59E0B]', 'text-[#DC2626]'],
            diff: ['text-[#16A34A]', 'text-[#DC2626]'],
        };
        document.querySelectorAll('[data-threshold]').forEach(el => {
            const type = el.dataset.threshold;
            const metricKey = el.dataset.metricValue;
            if (!type || !metricKey) return;
            const value = values[metricKey];
            if (value === undefined || value === null) return;
            const removable = thresholdClassMap[type] || [];
            removable.forEach(cls => el.classList.remove(cls));
            let cls = null;
            if (type === 'acv') {
                cls = value < 70 ? 'text-[#DC2626]' : (value < 85 ? 'text-[#F59E0B]' : (value <= 110 ? 'text-[#16A34A]' : 'text-[#2563EB]'));
            } else if (type === 'refraksi') {
                cls = value <= 1 ? 'text-[#16A34A]' : (value <= 2 ? 'text-[#F59E0B]' : 'text-[#DC2626]');
            } else if (type === 'diff') {
                cls = value >= 0 ? 'text-[#16A34A]' : 'text-[#DC2626]';
            }
            if (cls) {
                el.classList.add(cls);
            }
        });
    }

    function normalizeNumber(value) {
        const number = Number.parseFloat(value);
        return Number.isFinite(number) ? number : 0;
    }

    function formatNumber(value, digits = 2) {
        const number = Number.isFinite(value) ? value : 0;
        return number.toLocaleString(undefined, { minimumFractionDigits: digits, maximumFractionDigits: digits });
    }

    function formatInteger(value) {
        const number = Number.isFinite(value) ? value : 0;
        return Math.round(number).toLocaleString();
    }

    function resolvePeriod(filters = {}) {
        const now = new Date();
        const year = Number.parseInt(filters.tahun ?? now.getFullYear());
        const monthName = (filters.bulan ?? '').toString().toUpperCase();
        const month = monthMap[monthName] ?? (now.getMonth() + 1);
        return { year, month };
    }

    function lastDayOfMonth(year, month) {
        return new Date(year, month, 0).getDate();
    }

    function buildDaySeries(series, key, year, month) {
        const days = lastDayOfMonth(year, month);
        const byDay = new Map();
        series.forEach(item => {
            const date = new Date(item.tanggal_panen);
            if (!(date instanceof Date) || Number.isNaN(date.getTime())) return;
            const raw = Number.parseFloat(item[key]);
            if (!Number.isFinite(raw)) return;
            byDay.set(date.getDate(), raw);
        });
        const labels = Array.from({ length: days }, (_, index) => String(index + 1).padStart(2, '0'));
        const data = labels.map((_, index) => {
            const day = index + 1;
            return byDay.has(day) ? byDay.get(day) : null;
        });
        return { labels, data };
    }

    function updateCharts(chartData = {}, filters = {}) {
        const period = resolvePeriod(filters);
        updatePksBudgetChart(chartData.daily_pks_budget ?? [], period);
        updateAkpChart(chartData.akp_daily ?? [], period);
    }

    function updatePksBudgetChart(series, period) {
        const canvas = document.getElementById('chartPksBudget');
        if (!canvas) return;
        const actualSeries = buildDaySeries(series, 'total_pks', period.year, period.month);
        const budgetSeries = buildDaySeries(series, 'total_budget', period.year, period.month);
        const labels = actualSeries.labels;
        const actualData = actualSeries.data.map(value => (value === null ? null : Number.parseFloat(value.toFixed(2))));
        const budgetData = budgetSeries.data.map(value => (value === null ? null : Number.parseFloat(value.toFixed(2))));
        if (!charts.pksBudget) {
            charts.pksBudget = createPksBudgetChart(canvas, labels, actualData, budgetData);
        } else {
            charts.pksBudget.data.labels = labels;
            charts.pksBudget.data.datasets[0].data = actualData;
            charts.pksBudget.data.datasets[1].data = budgetData;
            charts.pksBudget.update('none');
        }
    }

    function createPksBudgetChart(canvas, labels, actual, budget) {
        const ctx = canvas.getContext('2d');
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'PKS Realisasi',
                        data: actual,
                        backgroundColor: 'rgba(22, 163, 74, 0.75)',
                        borderRadius: 6,
                        maxBarThickness: 32,
                        order: 2,
                    },
                    {
                        type: 'line',
                        label: 'Budget Harian',
                        data: budget,
                        borderColor: APP_COLORS.blue,
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        borderDash: [6, 4],
                        spanGaps: true,
                        order: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#111827', usePointStyle: true, boxWidth: 8 },
                    },
                    tooltip: {
                        callbacks: {
                            label: context => `${context.dataset.label}: ${formatNumber(context.parsed.y ?? 0, 2)} kg`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#6B7280',
                            callback: value => formatInteger(value),
                        },
                        grid: { color: APP_COLORS.border },
                    },
                    x: {
                        ticks: { color: '#6B7280' },
                        grid: { display: false },
                    },
                },
            },
        });
    }

    function updateAkpChart(series, period) {
        const canvas = document.getElementById('chartAkpDaily');
        if (!canvas) return;
        const akpSeries = buildDaySeries(series, 'akp_pct', period.year, period.month);
        const labels = akpSeries.labels;
        const data = akpSeries.data.map(value => {
            if (value === null) return null;
            const normalized = Number.parseFloat(value);
            return Number.isFinite(normalized) ? Number.parseFloat(normalized.toFixed(2)) : null;
        });
        if (!charts.akpDaily) {
            charts.akpDaily = createAkpChart(canvas, labels, data);
        } else {
            charts.akpDaily.data.labels = labels;
            charts.akpDaily.data.datasets[0].data = data;
            charts.akpDaily.update('none');
        }
    }

    function createAkpChart(canvas, labels, data) {
        const ctx = canvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, 'rgba(22, 163, 74, 0.3)');
        gradient.addColorStop(1, 'rgba(22, 163, 74, 0)');
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'AKP %',
                        data,
                        borderColor: APP_COLORS.green,
                        backgroundColor: gradient,
                        fill: 'start',
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        spanGaps: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: context => `${formatNumber(context.parsed.y ?? 0, 2)}%`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#6B7280', callback: value => `${formatNumber(value, 0)}%` },
                        grid: { color: APP_COLORS.border },
                    },
                    x: {
                        ticks: { color: '#6B7280' },
                        grid: { display: false },
                    },
                },
            },
        });
    }
});
</script>
@endpush

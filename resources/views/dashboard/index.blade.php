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
    <!-- Hero banner with greeting and active filters -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#1D4ED8] via-[#16A34A] to-[#059669] p-8 text-white shadow-lg">
        <div class="absolute inset-0 opacity-10">
            <div class="h-full w-full bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.4),_transparent_55%)]"></div>
        </div>
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-white/80">Dashboard Operasional</p>
                <h2 class="mt-2 text-3xl font-semibold">Selamat Datang, {{ $userName ?? 'User' }}</h2>
                <p class="mt-2 text-white/80">PT Sahabat Agro Group — {{ $todayFormatted ?? date('d F Y') }}</p>
            </div>
            <div class="flex flex-col gap-4 text-base">
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-sm font-semibold text-white">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-white"></span>
                        Data Live Railway
                    </span>
                    <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-sm text-white/90">
                        <i class="fas fa-clock text-white/70"></i>
                        <span class="ml-2">{{ $nowJakarta->format('d M Y, H:i') }} WIB</span>
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
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Filter Analitik</h3>
                        <p class="text-sm text-gray-500">Atur rentang data untuk memperbarui kartu & grafik.</p>
                    </div>
                    <span id="filterStatusBadge" class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-[#15803d] transition">Siap</span>
                </div>
                <form id="dashboardFilterForm" action="{{ route('dashboard') }}" method="GET" class="mt-6 space-y-4">
                    <div class="space-y-2">
                        <label for="kebun" class="block text-sm font-medium text-gray-900">Kebun</label>
                        <div class="relative">
                            <i class="fas fa-leaf pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <select name="kebun" id="kebun" class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm transition focus:border-[#16A34A] focus:ring-[#16A34A]">
                                <option value="">Semua Kebun</option>
                                @foreach(($kebunList ?? []) as $k)
                                    <option value="{{ $k }}" {{ request('kebun') == $k ? 'selected' : '' }}>{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="divisi" class="block text-sm font-medium text-gray-900">Divisi</label>
                        <div class="relative">
                            <i class="fas fa-diagram-project pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <select name="divisi" id="divisi" class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm transition focus:border-[#16A34A] focus:ring-[#16A34A]">
                                <option value="">Semua Divisi</option>
                                @foreach(($divisiList ?? []) as $d)
                                    <option value="{{ $d }}" {{ request('divisi') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="bulan" class="block text-sm font-medium text-gray-900">Bulan</label>
                        <div class="relative">
                            <i class="fas fa-calendar-days pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <select name="bulan" id="bulan" class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm transition focus:border-[#16A34A] focus:ring-[#16A34A]">
                                <option value="">Bulan Ini</option>
                                @foreach(($bulanList ?? []) as $b)
                                    <option value="{{ strtoupper($b) }}" {{ strtoupper((string)request('bulan')) === strtoupper((string)$b) ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="tahun" class="block text-sm font-medium text-gray-900">Tahun</label>
                        <div class="relative">
                            <i class="fas fa-calendar pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <select name="tahun" id="tahun" class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm transition focus:border-[#16A34A] focus:ring-[#16A34A]">
                                <option value="">Tahun Ini</option>
                                @for($y = ($yearNow ?? (int)date('Y'))+1; $y >= ($yearNow ?? (int)date('Y'))-5; $y--)
                                    <option value="{{ $y }}" {{ (string)request('tahun') === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" id="applyFilters" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-[#16A34A] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#15803d] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#15803d]">
                            <i class="fas fa-filter"></i>
                            Terapkan
                        </button>
                        <button type="button" id="resetFilters" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:border-gray-300 hover:text-gray-900">
                            <i class="fas fa-rotate-left"></i>
                            Reset
                        </button>
                    </div>
                    <p class="text-xs text-gray-400">Klik "Terapkan" untuk memperbarui data tanpa memuat ulang halaman.</p>
                </form>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Catatan Data</h3>
                <ul class="mt-4 space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-[#16A34A]"></span>
                        <span>Data harian dikonsolidasikan dari sistem panen PT SAG setiap pukul 05.00 WIB.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-[#2563EB]"></span>
                        <span>Pembaharuan filter akan memicu refresh kartu KPI, ringkasan bulanan, serta grafik produksi.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-[#F59E0B]"></span>
                        <span>Nilai KPI berwarna menunjukkan status capaian dibanding target operasional internal.</span>
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
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Monitoring Produksi Harian</h3>
                        <p class="text-sm text-gray-500">Ikhtisar kinerja panen & kualitas TBS hari ini.</p>
                    </div>
                    <span class="rounded-full bg-[#16A34A]/10 px-3 py-1 text-xs font-semibold text-[#15803d]">Update harian</span>
                </div>
                <div id="kpiTodayWrap" class="relative">
                    <div id="kpiTodayLoading" class="hidden absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/70 backdrop-blur-[1px]">
                        <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <article class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#F59E0B]/15 text-[#F59E0B]"><i class="fas fa-scale-balanced"></i></span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">BJR (Hari Ini)</p>
                                    <p class="text-xs text-gray-400">Rata-rata berat tandan</p>
                                </div>
                            </div>
                            <div class="mt-6 flex items-end justify-between">
                                <p class="text-3xl font-semibold text-[#F59E0B]" data-metric-value="today.bjr">{{ number_format($todayMetrics['bjr'] ?? 0, 2) }}</p>
                                <span class="text-sm text-gray-400">kg/jjg</span>
                            </div>
                        </article>
                        <article class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#2563EB]/15 text-[#2563EB]"><i class="fas fa-bullseye-arrow"></i></span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">AKP (Hari Ini)</p>
                                    <p class="text-xs text-gray-400">Realisasi angka kerapatan panen</p>
                                </div>
                            </div>
                            <div class="mt-6 flex items-end justify-between">
                                <p class="text-3xl font-semibold text-[#2563EB]" data-metric-value="today.akp_pct">{{ number_format(($todayMetrics['akp'] ?? 0) * 100, 2) }}%</p>
                                <span class="text-sm text-gray-400">%</span>
                            </div>
                        </article>
                        <article class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#0EA5E9]/15 text-[#0EA5E9]"><i class="fas fa-people-group"></i></span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">HK (Hari Ini)</p>
                                    <p class="text-xs text-gray-400">Total tenaga kerja panen</p>
                                </div>
                            </div>
                            <div class="mt-6 flex items-end justify-between">
                                <p class="text-3xl font-semibold text-gray-900" data-metric-value="today.hk">{{ number_format($todayMetrics['total_tk'] ?? 0) }}</p>
                                <span class="text-sm text-gray-400">orang</span>
                            </div>
                        </article>
                        <article class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#16A34A]/15 text-[#16A34A]"><i class="fas fa-chart-line"></i></span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">ACV Prod (Hari Ini)</p>
                                    <p class="text-xs text-gray-400">Perbandingan realisasi terhadap target</p>
                                </div>
                            </div>
                            <div class="mt-6 flex items-end justify-between">
                                <p class="text-3xl font-semibold {{ $acvColor }}" data-metric-value="today.acv_prod" data-threshold="acv">{{ number_format($acv, 2) }}%</p>
                                <span class="text-sm text-gray-400">%</span>
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
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Kinerja Timbang & Kualitas</h3>
                        <p class="text-sm text-gray-500">Produksi, selisih timbang, serta kualitas refraksi TBS.</p>
                    </div>
                </div>
                <div id="kpiSecondaryWrap" class="relative">
                    <div id="kpiSecondaryLoading" class="hidden absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/70 backdrop-blur-[1px]">
                        <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">Total Produksi (kg)</p>
                                    <p class="text-xs text-gray-400">Realisasi panen hari ini</p>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-[#16A34A]/10 px-2.5 py-1 text-xs font-medium text-[#15803d]">Volume</span>
                            </div>
                            <p class="mt-6 text-3xl font-semibold text-gray-900" data-metric-value="today.total_produksi">{{ number_format($todayMetrics['total_produksi'] ?? 0, 2) }}</p>
                        </article>
                        <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">Selisih Timbang</p>
                                    <p class="text-xs text-gray-400">Perbandingan timbangan panen vs PKS</p>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-[#2563EB]/10 px-2.5 py-1 text-xs font-medium text-[#2563EB]">Audit</span>
                            </div>
                            <div class="mt-6 flex items-end justify-between">
                                <p class="text-3xl font-semibold {{ $selColor }}" data-metric-value="today.selisih" data-threshold="diff">{{ number_format($sel ?? 0, 2) }}</p>
                                <div class="text-right text-sm text-gray-500">
                                    <span class="font-semibold text-gray-600">Persen</span>
                                    <div><span data-metric-value="today.selisih_percent">{{ number_format($todayMetrics['selisih_persen'] ?? 0, 2) }}</span>%</div>
                                </div>
                            </div>
                        </article>
                        <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">Refraksi</p>
                                    <p class="text-xs text-gray-400">Persentase & bobot potongan</p>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-[#F59E0B]/10 px-2.5 py-1 text-xs font-medium text-[#F59E0B]">Kualitas</span>
                            </div>
                            <div class="mt-6 space-y-2">
                                <p class="text-3xl font-semibold {{ $refColor }}" data-metric-value="today.refraksi_percent" data-threshold="refraksi">{{ number_format($todayMetrics['refraksi_persen'] ?? 0, 2) }}%</p>
                                <p class="text-sm text-gray-500"><span data-metric-value="today.refraksi_kg">{{ number_format($todayMetrics['refraksi_kg'] ?? 0, 2) }}</span> kg</p>
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
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Bulanan</h3>
                        <p id="monthlySummaryTitle" class="text-sm text-gray-500" data-metric-value="summary.title">{{ $summaryTitle }}</p>
                    </div>
                    <span class="rounded-full bg-[#2563EB]/10 px-3 py-1 text-xs font-semibold text-[#2563EB]">Agregasi Bulanan</span>
                </div>
                <div id="kpiMonthlyWrap" class="relative">
                    <div id="kpiMonthlyLoading" class="hidden absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/70 backdrop-blur-[1px]">
                        <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
                    </div>
                    <div class="space-y-5">
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-sm font-semibold text-gray-600">BJR (Bulan)</p>
                                <p class="mt-4 text-3xl font-semibold text-[#F59E0B]" data-metric-value="monthly.bjr">{{ number_format($monthlyMetrics['bjr'] ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-400">Rata-rata berat tandan</p>
                            </article>
                            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-sm font-semibold text-gray-600">AKP (Bulan)</p>
                                <p class="mt-4 text-3xl font-semibold text-[#2563EB]" data-metric-value="monthly.akp_pct">{{ number_format(($monthlyMetrics['akp'] ?? 0) * 100, 2) }}%</p>
                                <p class="text-xs text-gray-400">Rata-rata kerapatan panen</p>
                            </article>
                            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-sm font-semibold text-gray-600">Total Produksi PKS (Bulan)</p>
                                <p class="mt-4 text-3xl font-semibold text-gray-900" data-metric-value="monthly.total_produksi">{{ number_format($monthlyMetrics['total_produksi'] ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-400">Akumulasi volume TBS</p>
                            </article>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-sm font-semibold text-gray-600">ACV Prod (Bulan)</p>
                                <p class="mt-4 text-3xl font-semibold {{ $macvColor }}" data-metric-value="monthly.acv_prod" data-threshold="acv">{{ number_format($macv, 2) }}%</p>
                                <p class="text-xs text-gray-400">Realisasi dibanding target</p>
                            </article>
                            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-sm font-semibold text-gray-600">Refraksi (kg & %)</p>
                                <div class="mt-4 space-y-2">
                                    <p class="text-3xl font-semibold {{ $mrefColor }}" data-metric-value="monthly.refraksi_percent" data-threshold="refraksi">{{ number_format($mref, 2) }}%</p>
                                    <p class="text-sm text-gray-500"><span data-metric-value="monthly.refraksi_kg">{{ number_format($monthlyMetrics['refraksi_kg'] ?? 0, 2) }}</span> kg</p>
                                </div>
                            </article>
                            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-sm font-semibold text-gray-600">Restan (jjg & %)</p>
                                <div class="mt-4 space-y-2">
                                    <p class="text-3xl font-semibold text-[#DC2626]" data-metric-value="monthly.restan_jjg">{{ number_format($monthlyMetrics['restan_jjg'] ?? 0) }}</p>
                                    <p class="text-sm text-gray-500"><span data-metric-value="monthly.restan_percent">{{ number_format($monthlyMetrics['restan_persen'] ?? 0, 2) }}</span>%</p>
                                </div>
                            </article>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-sm font-semibold text-gray-600">JJG / PKK</p>
                                <p class="mt-4 text-3xl font-semibold text-gray-900" data-metric-value="monthly.jjg_per_pkk">{{ number_format($monthlyMetrics['jjg_per_pkk'] ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-400">Total PKK: <span data-metric-value="monthly.total_pkk">{{ number_format($monthlyMetrics['total_pkk'] ?? 0) }}</span></p>
                            </article>
                            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-sm font-semibold text-gray-600">Ha / HK</p>
                                <p class="mt-4 text-3xl font-semibold text-gray-900" data-metric-value="monthly.ha_per_hk">{{ number_format($monthlyMetrics['ha_per_hk'] ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-400">Efisiensi pemakaian tenaga kerja</p>
                            </article>
                            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-sm font-semibold text-gray-600">Ton / HK</p>
                                <p class="mt-4 text-3xl font-semibold text-gray-900" data-metric-value="monthly.ton_per_hk">{{ number_format($monthlyMetrics['ton_per_hk'] ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-400">Produktivitas tonase per tenaga kerja</p>
                            </article>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Visualisasi Produksi</h3>
                        <p class="text-sm text-gray-500">Perbandingan PKS vs budget dan tren realisasi AKP.</p>
                    </div>
                </div>
                <div class="grid gap-6 lg:grid-cols-2">
                    <div id="chartPksBudgetContainer" class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="text-base font-semibold text-gray-900">PKS vs Budget Harian</h4>
                                <p class="text-sm text-gray-500">Monitoring output gudang terhadap rencana anggaran.</p>
                            </div>
                            <span class="rounded-full bg-[#2563EB]/10 px-3 py-1 text-xs font-semibold text-[#2563EB]">Produksi</span>
                        </div>
                        <div class="relative mt-6 h-[320px]">
                            <div id="chartPksBudgetLoading" class="hidden absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/80 backdrop-blur-[1px]">
                                <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
                            </div>
                            <canvas id="chartPksBudget" class="h-full w-full"></canvas>
                        </div>
                    </div>
                    <div id="chartAkpDailyContainer" class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="text-base font-semibold text-gray-900">Rasio AKP (%) per Hari</h4>
                                <p class="text-sm text-gray-500">Tren kualitas panen berdasarkan angka kerapatan panen.</p>
                            </div>
                            <span class="rounded-full bg-[#16A34A]/10 px-3 py-1 text-xs font-semibold text-[#15803d]">Kualitas</span>
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

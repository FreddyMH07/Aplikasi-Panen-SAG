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

    <!-- Filter Chips & Summary Title -->
    <div class="flex flex-col gap-3">
        <div class="flex flex-wrap items-center gap-2">
            @php
                $hasAny = !empty($selectedFilters['kebun']) || !empty($selectedFilters['divisi']) || !empty($selectedFilters['bulan']) || !empty($selectedFilters['tahun']);
            @endphp
            @if($hasAny)
                <span class="text-sm text-gray-600 dark:text-gray-300 mr-1">Filter aktif:</span>
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
                <a href="{{ route('dashboard') }}" class="ml-2 text-sm px-3 py-1 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Reset Filter</a>
            @endif
        </div>
        @if(!empty($summaryTitle))
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $summaryTitle }}</h3>
        @endif
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

    <!-- Quick Stats Chips -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col gap-3">
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Hari Ini</h4>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs border border-green-200">ACV {{ number_format($todayMetrics['acv_prod'] ?? 0, 2) }}%</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs border border-blue-200">AKP {{ number_format(($todayMetrics['akp'] ?? 0) * 100, 2) }}%</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs border border-amber-200">BJR {{ number_format($todayMetrics['bjr'] ?? 0, 2) }}</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs border border-rose-200">Restan {{ number_format($todayMetrics['restan_persen'] ?? 0, 2) }}%</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs border border-purple-200">Refraksi {{ number_format($todayMetrics['refraksi_persen'] ?? 0, 2) }}%</span>
                </div>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Bulan Ini</h4>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs border border-green-200">ACV {{ number_format($monthlyMetrics['acv_prod'] ?? 0, 2) }}%</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs border border-blue-200">AKP {{ number_format(($monthlyMetrics['akp'] ?? 0) * 100, 2) }}%</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-sky-50 text-sky-700 text-xs border border-sky-200">JJG/PKK {{ number_format($monthlyMetrics['jjg_per_pkk'] ?? 0, 2) }}</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs border border-amber-200">Ha/HK {{ number_format($monthlyMetrics['ha_per_hk'] ?? 0, 2) }}</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-fuchsia-50 text-fuchsia-700 text-xs border border-fuchsia-200">Ton/HK {{ number_format($monthlyMetrics['ton_per_hk'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Today vs Month Summary Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
        <h3 class="font-semibold mb-3">Ringkasan: Hari Ini vs Bulan Ini</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-600 dark:text-gray-300">
                        <th class="py-2 pr-4">Metrik</th>
                        <th class="py-2 pr-4">Hari Ini</th>
                        <th class="py-2 pr-4">Bulan Ini</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800 dark:text-gray-100">
                    <tr>
                        <td class="py-2 pr-4">BJR</td>
                        <td class="py-2 pr-4">{{ number_format($todayMetrics['bjr'] ?? 0, 2) }}</td>
                        <td class="py-2 pr-4">{{ number_format($monthlyMetrics['bjr'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4">AKP (%)</td>
                        <td class="py-2 pr-4">{{ number_format(($todayMetrics['akp'] ?? 0) * 100, 2) }}%</td>
                        <td class="py-2 pr-4">{{ number_format(($monthlyMetrics['akp'] ?? 0) * 100, 2) }}%</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4">HK</td>
                        <td class="py-2 pr-4">{{ number_format($todayMetrics['total_tk'] ?? 0) }}</td>
                        <td class="py-2 pr-4">{{ number_format($monthlyMetrics['total_tk'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4">Produksi PKS (kg)</td>
                        <td class="py-2 pr-4">{{ number_format($todayMetrics['total_produksi'] ?? 0, 2) }}</td>
                        <td class="py-2 pr-4">{{ number_format($monthlyMetrics['total_produksi'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4">ACV Produksi (%)</td>
                        <td class="py-2 pr-4">{{ number_format($todayMetrics['acv_prod'] ?? 0, 2) }}%</td>
                        <td class="py-2 pr-4">{{ number_format($monthlyMetrics['acv_prod'] ?? 0, 2) }}%</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4">Refraksi</td>
                        <td class="py-2 pr-4">{{ number_format($todayMetrics['refraksi_kg'] ?? 0, 2) }} kg • {{ number_format($todayMetrics['refraksi_persen'] ?? 0, 2) }}%</td>
                        <td class="py-2 pr-4">{{ number_format($monthlyMetrics['refraksi_kg'] ?? 0, 2) }} kg • {{ number_format($monthlyMetrics['refraksi_persen'] ?? 0, 2) }}%</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4">Restan</td>
                        <td class="py-2 pr-4">{{ number_format($todayMetrics['restan_jjg'] ?? 0, 2) }} JJG • {{ number_format($todayMetrics['restan_persen'] ?? 0, 2) }}%</td>
                        <td class="py-2 pr-4">{{ number_format($monthlyMetrics['restan_jjg'] ?? 0, 2) }} JJG • {{ number_format($monthlyMetrics['restan_persen'] ?? 0, 2) }}%</td>
                    </tr>
                </tbody>
            </table>
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

    <!-- Daily PKS vs Budget Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
        <h3 class="font-semibold mb-3">Tabel Harian: PKS vs Budget</h3>
        <div class="overflow-x-auto">
            <table id="tblPksBudget" class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-600 dark:text-gray-300">
                        <th class="py-2 pr-4">Tanggal</th>
                        <th class="py-2 pr-4">PKS (kg)</th>
                        <th class="py-2 pr-4">Budget (kg)</th>
                        <th class="py-2 pr-4">Selisih (kg)</th>
                        <th class="py-2 pr-4">ACV (%)</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800 dark:text-gray-100">
                    @foreach(($chartData['daily_pks_budget'] ?? []) as $row)
                        @php
                            $pks = (float)($row['total_pks'] ?? $row->total_pks ?? 0);
                            $budget = (float)($row['total_budget'] ?? $row->total_budget ?? 0);
                            $selisih = $pks - $budget;
                            $acv = $budget > 0 ? ($pks / $budget) * 100 : 0;
                            $tgl = is_array($row) ? ($row['tanggal_panen'] ?? '') : ($row->tanggal_panen ?? '');
                        @endphp
                        <tr>
                            <td class="py-2 pr-4">{{ $tgl }}</td>
                            <td class="py-2 pr-4">{{ number_format($pks, 2) }}</td>
                            <td class="py-2 pr-4">{{ number_format($budget, 2) }}</td>
                            <td class="py-2 pr-4">{{ number_format($selisih, 2) }}</td>
                            <td class="py-2 pr-4">{{ number_format($acv, 2) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

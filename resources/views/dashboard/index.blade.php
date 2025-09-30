@extends('layouts.app')

@section('title', 'Dashboard - PT Sahabat Agro Group')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header with greeting and date -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Selamat Datang, {{ $userName ?? 'User' }}</h2>
        <p class="text-white/80 mt-1">PT Sahabat Agro Group — {{ $todayFormatted ?? date('d F Y') }}</p>
    </div>

    <!-- Filters: Kebun, Divisi, Bulan, Tahun -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
        </form>
    </div>

    <!-- KPI Harian Cards: BJR, AKP, HK, ACV Prod -->
    @php
        $acv = (float)($todayMetrics['acv_prod'] ?? 0);
        $acvColor = $acv < 70 ? 'text-red-600' : ($acv < 85 ? 'text-amber-600' : ($acv <= 110 ? 'text-green-600' : 'text-blue-600'));
        $refPct = (float)($todayMetrics['refraksi_persen'] ?? 0);
        $refColor = $refPct <= 1 ? 'text-green-600' : ($refPct <= 2 ? 'text-amber-600' : 'text-red-600');
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">BJR (Hari Ini)</div>
            <div class="mt-1 text-2xl font-semibold text-amber-600">{{ number_format($todayMetrics['bjr'] ?? 0, 2) }}</div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">AKP (Hari Ini)</div>
            <div class="mt-1 text-2xl font-semibold text-blue-600">{{ number_format(($todayMetrics['akp'] ?? 0) * 100, 2) }}%</div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">HK (Hari Ini)</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($todayMetrics['total_tk'] ?? 0) }}</div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">ACV Prod (Hari Ini)</div>
            <div class="mt-1 text-2xl font-semibold {{ $acvColor }}">{{ number_format($acv, 2) }}%</div>
        </div>
    </div>

    <!-- Secondary cards: Total Produksi, Selisih, Refraksi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">Total Produksi (kg)</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($todayMetrics['total_produksi'] ?? 0, 2) }}</div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">Selisih Timbang</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($todayMetrics['selisih'] ?? 0, 2) }} <span class="text-base text-gray-600">• {{ number_format($todayMetrics['selisih_persen'] ?? 0, 2) }}%</span></div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">Refraksi</div>
            <div class="mt-1 text-2xl font-semibold {{ $refColor }}">{{ number_format($todayMetrics['refraksi_persen'] ?? 0, 2) }}% <span class="text-gray-600 text-base">• {{ number_format($todayMetrics['refraksi_kg'] ?? 0, 2) }} kg</span></div>
        </div>
    </div>

    <!-- Monthly summary title -->
    @if(!empty($summaryTitle))
        <h3 class="text-lg font-semibold text-gray-900">{{ $summaryTitle }}</h3>
    @endif

    <!-- Monthly summary metrics grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">BJR (Bulan)</div>
            <div class="mt-1 text-2xl font-semibold text-amber-600">{{ number_format($monthlyMetrics['bjr'] ?? 0, 2) }}</div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">AKP (Bulan)</div>
            <div class="mt-1 text-2xl font-semibold text-blue-600">{{ number_format(($monthlyMetrics['akp'] ?? 0) * 100, 2) }}%</div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">Total Produksi PKS (Bulan)</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($monthlyMetrics['total_produksi'] ?? 0, 2) }} kg</div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">ACV Prod (Bulan)</div>
            @php $macv = (float)($monthlyMetrics['acv_prod'] ?? 0); $macvColor = $macv < 70 ? 'text-red-600' : ($macv < 85 ? 'text-amber-600' : ($macv <= 110 ? 'text-green-600' : 'text-blue-600')); @endphp
            <div class="mt-1 text-2xl font-semibold {{ $macvColor }}">{{ number_format($macv, 2) }}%</div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">Refraksi (kg & %)</div>
            @php $mref = (float)($monthlyMetrics['refraksi_persen'] ?? 0); $mrefColor = $mref <= 1 ? 'text-green-600' : ($mref <= 2 ? 'text-amber-600' : 'text-red-600'); @endphp
            <div class="mt-1 text-2xl font-semibold {{ $mrefColor }}">{{ number_format($monthlyMetrics['refraksi_kg'] ?? 0, 2) }} kg <span class="text-gray-600 text-base">• {{ number_format($mref, 2) }}%</span></div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">Restan (jjg & %)</div>
            <div class="mt-1 text-2xl font-semibold text-red-600">{{ number_format($monthlyMetrics['restan_jjg'] ?? 0) }} <span class="text-base text-gray-600">• {{ number_format($monthlyMetrics['restan_persen'] ?? 0, 2) }}%</span></div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">JJG / PKK</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($monthlyMetrics['jjg_per_pkk'] ?? 0, 2) }} <span class="text-sm text-gray-600">(Total PKK: {{ number_format($monthlyMetrics['total_pkk'] ?? 0) }})</span></div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">Ha / HK</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($monthlyMetrics['ha_per_hk'] ?? 0, 2) }}</div>
        </div>
        <div class="p-4 bg-white rounded-lg border border-gray-200">
            <div class="text-xs uppercase text-gray-600">Ton / HK</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($monthlyMetrics['ton_per_hk'] ?? 0, 2) }}</div>
        </div>
    </div>

    <!-- Charts: PKS vs Budget, AKP Daily -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="text-sm font-semibold text-gray-900 mb-2">PKS vs Budget (per Hari)</div>
            <canvas id="chartPksBudget" height="160"></canvas>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="text-sm font-semibold text-gray-900 mb-2">Realisasi AKP (%)</div>
            <canvas id="chartAkpDaily" height="160"></canvas>
        </div>
    </div>

    @push('scripts')
    <script>
    // Auto-refresh on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action="{{ route('dashboard') }}"]');
        if (form) {
            ['kebun','divisi','bulan','tahun'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.addEventListener('change', () => form.submit());
            });
        }
    });

    // Safe data for charts
    const dailyPksBudget = @json($chartData['daily_pks_budget'] ?? []);
    const akpDaily = @json($chartData['akp_daily'] ?? []);

    function toLabels(series) {
        return series.map(d => (d.tanggal_panen || '').slice(8,10));
    }
    function toData(series, key) {
        return series.map(d => Number(d[key] || 0));
    }

    // PKS vs Budget (Bar + Line)
    (function() {
        const ctx = document.getElementById('chartPksBudget');
        if (!ctx) return;
        const labels = toLabels(dailyPksBudget);
        const pks = toData(dailyPksBudget, 'total_pks');
        const budget = toData(dailyPksBudget, 'total_budget');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'PKS',
                        data: pks,
                        backgroundColor: APP_COLORS.green[100],
                        borderColor: APP_COLORS.green[600],
                        borderWidth: 1,
                    },
                    {
                        type: 'line',
                        label: 'Budget',
                        data: budget,
                        borderColor: APP_COLORS.blue[600],
                        backgroundColor: APP_COLORS.blue[600] + '20',
                        borderWidth: 2,
                        tension: 0.3,
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, grid: { color: '#eee' } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { labels: { color: '#111827' } } }
            }
        });
    })();

    // AKP Daily (%)
    (function() {
        const ctx = document.getElementById('chartAkpDaily');
        if (!ctx) return;
        const labels = toLabels(akpDaily);
        const akpPct = akpDaily.map(d => Number(d.akp_pct || 0).toFixed(2));
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'AKP %',
                    data: akpPct,
                    borderColor: APP_COLORS.blue[600],
                    backgroundColor: APP_COLORS.blue[600] + '10',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, grid: { color: '#eee' } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { labels: { color: '#111827' } } }
            }
        });
    })();
    </script>
    @endpush
</div>
@endsection

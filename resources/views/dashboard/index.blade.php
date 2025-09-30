@extends('layouts.app')

@section('title', 'Dashboard - PT Sahabat Agro Group')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header with greeting and date (light-only) -->
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="text-2xl font-semibold text-gray-900">Selamat Datang, {{ $userName ?? 'User' }}</h2>
        <p class="text-gray-600 mt-1">PT Sahabat Agro Group — {{ $todayFormatted ?? date('d F Y') }}</p>
    </div>

    <!-- Filters: Kebun, Divisi, Bulan, Tahun -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
    <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label for="kebun" class="block text-sm font-medium text-gray-900 mb-1">Kebun</label>
                <select name="kebun" id="kebun" class="w-full rounded-lg border-gray-300 hover:border-[#16A34A] focus:border-[#16A34A] focus:ring-[#16A34A] outline-none transition">
                    <option value="">Semua Kebun</option>
                    @foreach(($kebunList ?? []) as $k)
                        <option value="{{ $k }}" {{ request('kebun') == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="divisi" class="block text-sm font-medium text-gray-900 mb-1">Divisi</label>
                <select name="divisi" id="divisi" class="w-full rounded-lg border-gray-300 hover:border-[#16A34A] focus:border-[#16A34A] focus:ring-[#16A34A] outline-none transition">
                    <option value="">Semua Divisi</option>
                    @foreach(($divisiList ?? []) as $d)
                        <option value="{{ $d }}" {{ request('divisi') == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Bulan</label>
                <select name="bulan" id="bulan" class="w-full rounded-lg border-gray-300 hover:border-[#16A34A] focus:border-[#16A34A] focus:ring-[#16A34A] outline-none transition">
                    <option value="">Bulan Ini</option>
                    @foreach(($bulanList ?? []) as $b)
                        <option value="{{ strtoupper($b) }}" {{ strtoupper((string)request('bulan')) === strtoupper((string)$b) ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Tahun</label>
                <select name="tahun" id="tahun" class="w-full rounded-lg border-gray-300 hover:border-[#16A34A] focus:border-[#16A34A] focus:ring-[#16A34A] outline-none transition">
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
        $acvColor = $acv < 70 ? 'text-[#DC2626]' : ($acv < 85 ? 'text-[#F59E0B]' : ($acv <= 110 ? 'text-[#16A34A]' : 'text-[#2563EB]'));
        $refPct = (float)($todayMetrics['refraksi_persen'] ?? 0);
        $refColor = $refPct <= 1 ? 'text-[#16A34A]' : ($refPct <= 2 ? 'text-[#F59E0B]' : 'text-[#DC2626]');
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">BJR (Hari Ini)</div>
            <div class="mt-1 text-2xl font-bold text-[#F59E0B]">{{ number_format($todayMetrics['bjr'] ?? 0, 2) }}</div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">AKP (Hari Ini)</div>
            <div class="mt-1 text-2xl font-bold text-[#2563EB]">{{ number_format(($todayMetrics['akp'] ?? 0) * 100, 2) }}%</div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">HK (Hari Ini)</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($todayMetrics['total_tk'] ?? 0) }}</div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">ACV Prod (Hari Ini)</div>
            <div class="mt-1 text-2xl font-bold {{ $acvColor }}">{{ number_format($acv, 2) }}%</div>
        </div>
    </div>

    <!-- Secondary cards: Total Produksi, Selisih, Refraksi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">Total Produksi (kg)</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($todayMetrics['total_produksi'] ?? 0, 2) }}</div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">Selisih Timbang</div>
            @php $sel = (float)($todayMetrics['selisih'] ?? 0); $selColor = $sel >= 0 ? 'text-[#16A34A]' : 'text-[#DC2626]'; @endphp
            <div class="mt-1 text-2xl font-bold {{ $selColor }}">{{ number_format($sel ?? 0, 2) }} <span class="text-base text-gray-600">• {{ number_format($todayMetrics['selisih_persen'] ?? 0, 2) }}%</span></div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">Refraksi</div>
            <div class="mt-1 text-2xl font-bold {{ $refColor }}">{{ number_format($todayMetrics['refraksi_persen'] ?? 0, 2) }}% <span class="text-gray-600 text-base">• {{ number_format($todayMetrics['refraksi_kg'] ?? 0, 2) }} kg</span></div>
        </div>
    </div>

    <!-- Monthly summary title -->
    @if(!empty($summaryTitle))
    <h3 class="text-lg font-semibold text-gray-900">{{ $summaryTitle }}</h3>
    @endif

    <!-- Monthly summary metrics grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">BJR (Bulan)</div>
            <div class="mt-1 text-2xl font-bold text-[#F59E0B]">{{ number_format($monthlyMetrics['bjr'] ?? 0, 2) }}</div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">AKP (Bulan)</div>
            <div class="mt-1 text-2xl font-bold text-[#2563EB]">{{ number_format(($monthlyMetrics['akp'] ?? 0) * 100, 2) }}%</div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">Total Produksi PKS (Bulan)</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($monthlyMetrics['total_produksi'] ?? 0, 2) }} kg</div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">ACV Prod (Bulan)</div>
            @php $macv = (float)($monthlyMetrics['acv_prod'] ?? 0); $macvColor = $macv < 70 ? 'text-[#DC2626]' : ($macv < 85 ? 'text-[#F59E0B]' : ($macv <= 110 ? 'text-[#16A34A]' : 'text-[#2563EB]')); @endphp
            <div class="mt-1 text-2xl font-bold {{ $macvColor }}">{{ number_format($macv, 2) }}%</div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">Refraksi (kg & %)</div>
            @php $mref = (float)($monthlyMetrics['refraksi_persen'] ?? 0); $mrefColor = $mref <= 1 ? 'text-[#16A34A]' : ($mref <= 2 ? 'text-[#F59E0B]' : 'text-[#DC2626]'); @endphp
            <div class="mt-1 text-2xl font-bold {{ $mrefColor }}">{{ number_format($monthlyMetrics['refraksi_kg'] ?? 0, 2) }} kg <span class="text-gray-600 text-base">• {{ number_format($mref, 2) }}%</span></div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">Restan (jjg & %)</div>
            <div class="mt-1 text-2xl font-bold text-[#DC2626]">{{ number_format($monthlyMetrics['restan_jjg'] ?? 0) }} <span class="text-base text-gray-600">• {{ number_format($monthlyMetrics['restan_persen'] ?? 0, 2) }}%</span></div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">JJG / PKK</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($monthlyMetrics['jjg_per_pkk'] ?? 0, 2) }} <span class="text-sm text-gray-600">(Total PKK: {{ number_format($monthlyMetrics['total_pkk'] ?? 0) }})</span></div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">Ha / HK</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($monthlyMetrics['ha_per_hk'] ?? 0, 2) }}</div>
        </div>
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900">Ton / HK</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($monthlyMetrics['ton_per_hk'] ?? 0, 2) }}</div>
        </div>
    </div>

    <!-- Charts: PKS vs Budget, AKP Daily -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-900 mb-2">PKS vs Budget (per Hari)</div>
            <canvas id="chartPksBudget" height="160"></canvas>
        </div>
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
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
        return series.map((d, i) => formatDayLabel(d?.tanggal_panen, i));
    }
    function toData(series, key) {
        return series.map(d => {
            const v = d?.[key];
            const n = Number.parseFloat(v);
            return Number.isFinite(n) ? n : 0;
        });
    }
    function formatDayLabel(val, idx) {
        // Try to parse as date, fallback to last 2 chars, then index+1
        if (val) {
            const t = typeof val === 'string' ? val : String(val);
            const dt = new Date(t);
            if (!isNaN(dt.getTime())) {
                const day = String(dt.getDate()).padStart(2, '0');
                return day;
            }
            if (t.length >= 2) return t.slice(-2);
        }
        return String((idx + 1)).padStart(2, '0');
    }
    function drawNoDataMessage(canvas, message = 'Tidak ada data') {
        const ctx2d = canvas.getContext('2d');
        if (!ctx2d) return;
        const { width, height } = canvas;
        ctx2d.clearRect(0, 0, width, height);
        ctx2d.save();
        ctx2d.fillStyle = '#6B7280';
        ctx2d.font = '14px system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial';
        ctx2d.textAlign = 'center';
        ctx2d.textBaseline = 'middle';
        ctx2d.fillText(message, width / 2, height / 2);
        ctx2d.restore();
    }

    // PKS vs Budget (Bar + Line) — PKS green bars (no outline), Budget blue line with small points
    (function() {
        const ctx = document.getElementById('chartPksBudget');
        if (!ctx) return;
        const labels = toLabels(dailyPksBudget);
        const pks = toData(dailyPksBudget, 'total_pks');
        const budget = toData(dailyPksBudget, 'total_budget');
    // Destroy existing chart instance if present (avoids duplicate/overlay bugs)
    const existing = Chart.getChart ? Chart.getChart(ctx) : null;
    if (existing) existing.destroy();
    const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'PKS',
                        data: pks,
                        backgroundColor: APP_COLORS.green,
                        borderWidth: 0,
                        borderRadius: 4,
                    },
                    {
                        type: 'line',
                        label: 'Budget',
                        data: budget,
                        borderColor: APP_COLORS.blue,
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.3,
                        pointBackgroundColor: APP_COLORS.blue,
                        pointBorderColor: APP_COLORS.blue,
                        pointRadius: 2,
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { color: APP_COLORS.border } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { labels: { color: '#111827' } } }
            }
        });
        if (!labels.length) {
            drawNoDataMessage(ctx);
        }
    })();

    // AKP Daily (%) — green line, no fill, thin gridlines
    (function() {
        const ctx = document.getElementById('chartAkpDaily');
        if (!ctx) return;
        const labels = toLabels(akpDaily);
        const akpPct = akpDaily.map(d => {
            const n = Number.parseFloat(d?.akp_pct ?? 0);
            return Number.isFinite(n) ? Number(n.toFixed(2)) : 0;
        });
        // Destroy existing chart instance if present
        const existing = Chart.getChart ? Chart.getChart(ctx) : null;
        if (existing) existing.destroy();
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'AKP %',
                    data: akpPct,
                    borderColor: APP_COLORS.green,
                    backgroundColor: 'transparent',
                    fill: false,
                    tension: 0.3,
                    pointRadius: 2,
                    pointBackgroundColor: APP_COLORS.green,
                    pointBorderColor: APP_COLORS.green,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { color: APP_COLORS.border } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { labels: { color: '#111827' } } }
            }
        });
        if (!labels.length) {
            drawNoDataMessage(ctx);
        }
    })();
    </script>
    @endpush
</div>
@endsection

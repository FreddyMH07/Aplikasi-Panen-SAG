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
    <div id="kpiTodayWrap" class="relative">
        <div id="kpiTodayLoading" class="hidden absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-10">
            <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
        </div>
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
    </div>

    <!-- Secondary cards: Total Produksi, Selisih, Refraksi -->
    <div id="kpiSecondaryWrap" class="relative">
        <div id="kpiSecondaryLoading" class="hidden absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-10">
            <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
        </div>
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
    </div>

    <!-- Monthly summary title -->
    @if(!empty($summaryTitle))
    <h3 class="text-lg font-semibold text-gray-900">{{ $summaryTitle }}</h3>
    @endif

    <!-- Monthly summary metrics grid -->
    <div id="kpiMonthlyWrap" class="relative">
        <div id="kpiMonthlyLoading" class="hidden absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-10">
            <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
        </div>
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
    </div>

    <!-- Charts: PKS vs Budget, AKP Daily -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div id="chartPksBudgetContainer" class="relative bg-white rounded-xl p-6 border border-gray-200 shadow-sm min-h-[320px]">
            <div id="chartPksBudgetLoading" class="hidden absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-10">
                <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
            </div>
            <div class="text-sm font-semibold text-gray-900 mb-2">PKS vs Budget (per Hari)</div>
            <canvas id="chartPksBudget" height="160"></canvas>
        </div>
        <div id="chartAkpDailyContainer" class="relative bg-white rounded-xl p-6 border border-gray-200 shadow-sm min-h-[320px]">
            <div id="chartAkpDailyLoading" class="hidden absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-10">
                <i class="fas fa-spinner animate-spin text-gray-400 text-xl"></i>
            </div>
            <div class="text-sm font-semibold text-gray-900 mb-2">Realisasi AKP (%)</div>
            <canvas id="chartAkpDaily" height="160"></canvas>
        </div>
    </div>

    @push('scripts')
    <script>
    // Debounced in-place filtering with JSON fetch
    const FILTER_IDS = ['kebun','divisi','bulan','tahun'];
    let fetchAbortController = null;
    function debounce(fn, wait=500){
        let t; return (...args)=>{ clearTimeout(t); t = setTimeout(()=>fn(...args), wait); };
    }
    function getFilters(){
        const o = {}; FILTER_IDS.forEach(id=>{ const el = document.getElementById(id); if (el && el.value) o[id]=el.value; }); return o;
    }
    function setLoading(section, on){
        const ids = {
            kpiToday: 'kpiTodayLoading',
            kpiSecondary: 'kpiSecondaryLoading',
            kpiMonthly: 'kpiMonthlyLoading',
            chartPksBudget: 'chartPksBudgetLoading',
            chartAkpDaily: 'chartAkpDailyLoading',
        };
        const id = ids[section]; if (!id) return;
        const el = document.getElementById(id); if (!el) return;
        el.classList.toggle('hidden', !on);
    }
    const monthMap = { JANUARI:1,FEBRUARI:2,MARET:3,APRIL:4,MEI:5,JUNI:6,JULI:7,AGUSTUS:8,SEPTEMBER:9,OKTOBER:10,NOVEMBER:11,DESEMBER:12, JANUARY:1,FEBRUARY:2,MARCH:3,MAY:5,JUNE:6,JULY:7,AUGUST:8,OCTOBER:10,DECEMBER:12 };
    function lastDayOfMonth(year, month1to12){ return new Date(year, month1to12, 0).getDate(); }
    function buildDaySeries(series, key, year, month1to12){
        const N = lastDayOfMonth(year, month1to12);
        const byDay = new Map();
        series.forEach(d=>{ const dt = new Date(d.tanggal_panen); if (!isNaN(dt)){ byDay.set(dt.getDate(), Number.parseFloat(d[key]) || 0); } });
        const labels = Array.from({length:N}, (_,i)=>String(i+1).padStart(2,'0'));
        const data = labels.map((_,i)=>{
            const day = i+1; return byDay.has(day) ? byDay.get(day) : null;
        });
        return { labels, data };
    }

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

    // Charts setup (persist instances and update in-place)
    const charts = { pksBudget: null, akpDaily: null };
    (function initCharts() {
        const ctx = document.getElementById('chartPksBudget');
        if (!ctx) return;
        // Infer year/month from filters or data
        const filtersInit = getFilters();
        const year = Number.parseInt(filtersInit.tahun || (new Date()).getFullYear());
        const monthNum = filtersInit.bulan ? (monthMap[(filtersInit.bulan+'').toUpperCase()] || (new Date()).getMonth()+1) : (new Date()).getMonth()+1;
        const builtPKS = buildDaySeries(dailyPksBudget, 'total_pks', year, monthNum);
        const builtBudget = buildDaySeries(dailyPksBudget, 'total_budget', year, monthNum);
        charts.pksBudget = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: builtPKS.labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'PKS',
                        data: builtPKS.data,
                        backgroundColor: APP_COLORS.green,
                        borderWidth: 0,
                        borderRadius: 4,
                    },
                    {
                        type: 'line',
                        label: 'Budget',
                        data: builtBudget.data,
                        borderColor: APP_COLORS.blue,
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.3,
                        pointBackgroundColor: APP_COLORS.blue,
                        pointBorderColor: APP_COLORS.blue,
                        pointRadius: 2,
                        spanGaps: true,
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
    })();

    (function initAkpChart() {
        const ctx = document.getElementById('chartAkpDaily');
        if (!ctx) return;
        const filtersInit = getFilters();
        const year = Number.parseInt(filtersInit.tahun || (new Date()).getFullYear());
        const monthNum = filtersInit.bulan ? (monthMap[(filtersInit.bulan+'').toUpperCase()] || (new Date()).getMonth()+1) : (new Date()).getMonth()+1;
        const byDay = new Map();
        akpDaily.forEach(d=>{ const dt=new Date(d.tanggal_panen); if(!isNaN(dt)){ byDay.set(dt.getDate(), Number.parseFloat(d.akp_pct)||0); }});
        const N = lastDayOfMonth(year, monthNum);
        const labels = Array.from({length:N}, (_,i)=>String(i+1).padStart(2,'0'));
        const data = labels.map((_,i)=>{ const day=i+1; return byDay.has(day) ? Number(byDay.get(day).toFixed(2)) : null; });
        charts.akpDaily = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'AKP %',
                    data,
                    borderColor: APP_COLORS.green,
                    backgroundColor: 'transparent',
                    fill: false,
                    tension: 0.3,
                    pointRadius: 2,
                    pointBackgroundColor: APP_COLORS.green,
                    pointBorderColor: APP_COLORS.green,
                    spanGaps: true,
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
    })();

    // Update UI helpers
    function updateText(selector, text){ const el=document.querySelector(selector); if(el) el.textContent=text; }
    function fmt(n, digits=2){ const num = Number.parseFloat(n); return Number.isFinite(num) ? num.toLocaleString(undefined, { minimumFractionDigits: digits, maximumFractionDigits: digits }) : '0'; }
    function fmtInt(n){ const num = Number.parseInt(n||0); return Number.isFinite(num) ? num.toLocaleString() : '0'; }

    // Apply response to cards and charts
    function applyDashboardData(json){
        // Today metrics
        const t = json.todayMetrics || {};
        updateText('#kpiTodayWrap .text-[#F59E0B]', fmt(t.bjr));
        updateText('#kpiTodayWrap .text-[#2563EB]', fmt((t.akp||0)*100));
        // HK value is the third card's number element
        const hkEl = document.querySelector('#kpiTodayWrap .grid > div:nth-child(3) .text-2xl'); if (hkEl) hkEl.textContent = fmtInt(t.total_tk);
        const acvEl = document.querySelector('#kpiTodayWrap .grid > div:nth-child(4) .text-2xl');
        if (acvEl) {
            acvEl.textContent = `${fmt(t.acv_prod)}%`;
            // ACV thresholds: <70 red, 70–85 amber, 85–110 green, >110 blue
            ['text-[#DC2626]','text-[#F59E0B]','text-[#16A34A]','text-[#2563EB]'].forEach(c=>acvEl.classList.remove(c));
            const v = Number.parseFloat(t.acv_prod||0);
            const cls = (v < 70) ? 'text-[#DC2626]' : (v < 85 ? 'text-[#F59E0B]' : (v <= 110 ? 'text-[#16A34A]' : 'text-[#2563EB]'));
            acvEl.classList.add(cls);
        }

        // Secondary
        const s = json.todayMetrics || {};
        const prodEl = document.querySelector('#kpiSecondaryWrap .grid > div:nth-child(1) .text-2xl'); if (prodEl) prodEl.textContent = fmt(s.total_produksi);
        const selisihEl = document.querySelector('#kpiSecondaryWrap .grid > div:nth-child(2) .text-2xl'); if (selisihEl) selisihEl.innerHTML = `${fmt(s.selisih)} <span class="text-base text-gray-600">• ${fmt(s.selisih_persen)}%</span>`;
        const refEl = document.querySelector('#kpiSecondaryWrap .grid > div:nth-child(3) .text-2xl');
        if (refEl) {
            refEl.innerHTML = `${fmt(s.refraksi_persen)}% <span class="text-gray-600 text-base">• ${fmt(s.refraksi_kg)} kg</span>`;
            // Refraksi: ≤1 green, ≤2 amber, >2 red
            ['text-[#16A34A]','text-[#F59E0B]','text-[#DC2626]'].forEach(c=>refEl.classList.remove(c));
            const rv = Number.parseFloat(s.refraksi_persen||0);
            refEl.classList.add(rv <= 1 ? 'text-[#16A34A]' : (rv <= 2 ? 'text-[#F59E0B]' : 'text-[#DC2626]'));
        }
        // Selisih warna: positive green, negative red
        const selisihElColor = document.querySelector('#kpiSecondaryWrap .grid > div:nth-child(2) .text-2xl');
        if (selisihElColor) {
            ['text-[#16A34A]','text-[#DC2626]'].forEach(c=>selisihElColor.classList.remove(c));
            const sv = Number.parseFloat(s.selisih||0);
            selisihElColor.classList.add(sv >= 0 ? 'text-[#16A34A]' : 'text-[#DC2626]');
        }

        // Monthly
        const m = json.monthlyMetrics || {};
        const mm = document.querySelectorAll('#kpiMonthlyWrap .grid > div .text-2xl');
        if (mm.length >= 8){
            mm[0].textContent = fmt(m.bjr);
            mm[1].textContent = `${fmt((m.akp||0)*100) }%`;
            mm[2].textContent = `${fmt(m.total_produksi)} kg`;
            mm[3].textContent = `${fmt(m.acv_prod)}%`;
            // ACV monthly color
            const acvMEl = mm[3];
            ['text-[#DC2626]','text-[#F59E0B]','text-[#16A34A]','text-[#2563EB]'].forEach(c=>acvMEl.classList.remove(c));
            const mv = Number.parseFloat(m.acv_prod||0);
            acvMEl.classList.add(mv < 70 ? 'text-[#DC2626]' : (mv < 85 ? 'text-[#F59E0B]' : (mv <= 110 ? 'text-[#16A34A]' : 'text-[#2563EB]')));
            mm[4].innerHTML = `${fmt(m.refraksi_kg)} kg <span class="text-gray-600 text-base">• ${fmt(m.refraksi_persen)}%</span>`;
            // Refraksi monthly color
            const refMEl = mm[4];
            ['text-[#16A34A]','text-[#F59E0B]','text-[#DC2626]'].forEach(c=>refMEl.classList.remove(c));
            const rvm = Number.parseFloat(m.refraksi_persen||0);
            refMEl.classList.add(rvm <= 1 ? 'text-[#16A34A]' : (rvm <= 2 ? 'text-[#F59E0B]' : 'text-[#DC2626]'));
            mm[5].innerHTML = `${fmtInt(m.restan_jjg)} <span class="text-base text-gray-600">• ${fmt(m.restan_persen)}%</span>`;
            mm[6].textContent = fmt(m.jjg_per_pkk);
            mm[7].textContent = fmt(m.ha_per_hk);
            const tonHK = document.querySelector('#kpiMonthlyWrap .grid > div:nth-child(9) .text-2xl'); if (tonHK) tonHK.textContent = fmt(m.ton_per_hk);
        }
        const titleEl = document.querySelector('h3.text-lg.font-semibold.text-gray-900'); if (titleEl && json.summaryTitle) titleEl.textContent = json.summaryTitle;

        // Charts
        const filters = json.selectedFilters || {};
        const year = Number.parseInt(filters.tahun || (new Date()).getFullYear());
        const monthNum = filters.bulan ? (monthMap[(filters.bulan+'').toUpperCase()] || (new Date()).getMonth()+1) : (new Date()).getMonth()+1;
        // PKS/Budget
        const builtPKS = buildDaySeries(json.chartData?.daily_pks_budget || [], 'total_pks', year, monthNum);
        const builtBudget = buildDaySeries(json.chartData?.daily_pks_budget || [], 'total_budget', year, monthNum);
        if (charts.pksBudget){
            charts.pksBudget.data.labels = builtPKS.labels;
            charts.pksBudget.data.datasets[0].data = builtPKS.data;
            charts.pksBudget.data.datasets[1].data = builtBudget.data;
            charts.pksBudget.update('none');
        }
        // AKP
        const akpSeries = json.chartData?.akp_daily || [];
        const byDay = new Map(); akpSeries.forEach(d=>{ const dt=new Date(d.tanggal_panen); if(!isNaN(dt)){ byDay.set(dt.getDate(), Number.parseFloat(d.akp_pct)||0); }});
        const N = lastDayOfMonth(year, monthNum);
        const labels = Array.from({length:N}, (_,i)=>String(i+1).padStart(2,'0'));
        const data = labels.map((_,i)=>{ const day=i+1; return byDay.has(day) ? Number(byDay.get(day).toFixed(2)) : null; });
        if (charts.akpDaily){
            charts.akpDaily.data.labels = labels;
            charts.akpDaily.data.datasets[0].data = data;
            charts.akpDaily.update('none');
        }
    }

    const debouncedFetch = debounce(async function(){
        const params = new URLSearchParams(getFilters());
        // Show loading overlays
        ['kpiToday','kpiSecondary','kpiMonthly','chartPksBudget','chartAkpDaily'].forEach(s=>setLoading(s, true));
        // Abort previous
        if (fetchAbortController) fetchAbortController.abort();
        fetchAbortController = new AbortController();
        try {
            const res = await fetch(`{{ route('dashboard.json') }}?${params.toString()}`, { signal: fetchAbortController.signal, headers: { 'Accept':'application/json' } });
            if (!res.ok) throw new Error('Network error');
            const json = await res.json();
            applyDashboardData(json);
        } catch (e) {
            if (e.name !== 'AbortError') console.warn('Fetch dashboard.json failed', e);
        } finally {
            ['kpiToday','kpiSecondary','kpiMonthly','chartPksBudget','chartAkpDaily'].forEach(s=>setLoading(s, false));
        }
    }, 600);

    document.addEventListener('DOMContentLoaded', function(){
        FILTER_IDS.forEach(id=>{ const el=document.getElementById(id); if (el) el.addEventListener('change', debouncedFetch); });
    });
    </script>
    @endpush
</div>
@endsection

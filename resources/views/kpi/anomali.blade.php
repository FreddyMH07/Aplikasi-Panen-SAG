@extends('layouts.app')

@section('content')
<div class="p-4">
  <h2 class="text-xl font-semibold mb-2">Anomali 3-Sigma</h2>
  @include('kpi._filters')
  <div class="mb-6">
    <canvas id="chartAnomali" height="110"></canvas>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    @foreach($stats as $metric => $s)
    <div class="p-3 border rounded">
      <div class="text-sm text-gray-500">{{ strtoupper(str_replace('_',' ',$metric)) }}</div>
      <div>Mean: {{ number_format($s['mean'] ?? 0, 3) }}, SD: {{ number_format($s['std'] ?? 0, 3) }}</div>
      <div class="text-xs">3σ band: {{ number_format($s['lower'] ?? 0,3) }} .. {{ number_format($s['upper'] ?? 0,3) }}</div>
    </div>
    @endforeach
  </div>
  <div class="overflow-x-auto">
    <table id="tblAnomali" class="min-w-full text-sm">
      <thead>
        <tr class="text-left border-b">
          <th class="py-2 pr-4">Tanggal</th>
          <th class="py-2 pr-4">Kebun</th>
          <th class="py-2 pr-4">Divisi</th>
          <th class="py-2 pr-4">Refraksi %</th>
          <th class="py-2 pr-4">Ketrek</th>
          <th class="py-2 pr-4">Loss %</th>
          <th class="py-2 pr-4">Bias AKP</th>
          <th class="py-2 pr-4">Bias BJR</th>
          <th class="py-2 pr-4">Flags</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data as $row)
        @php
          $flags = $row['flags'] ?? [];
        @endphp
        <tr class="border-b {{ !empty($flags) ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
          <td class="py-1 pr-4">{{ \Carbon\Carbon::parse($row['tanggal_panen'])->format('d M Y') }}</td>
          <td class="py-1 pr-4">{{ $row['kebun'] }}</td>
          <td class="py-1 pr-4">{{ $row['divisi'] }}</td>
          <td class="py-1 pr-4">{{ number_format($row['refraksi_persen'],2) }}%</td>
          <td class="py-1 pr-4">{{ number_format($row['ketrek'],2) }}</td>
          <td class="py-1 pr-4">{{ number_format($row['loss_pct'],2) }}%</td>
          <td class="py-1 pr-4">{{ number_format($row['akp_bias'],2) }}%</td>
          <td class="py-1 pr-4">{{ number_format($row['bjr_bias'],3) }}</td>
          <td class="py-1 pr-4">{{ implode(', ', array_map('strtoupper', $flags)) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
@push('scripts')
<script>
  (function(){
    const stats = @json($stats);
    const rows = @json($data);
    const metrics = Object.keys(stats);
    const counts = metrics.map(m => rows.filter(r => (r.flags||[]).includes(m)).length);
  window.__charts = window.__charts || [];
  const ch = (window.__makeChart||function(t,c){ try{const ch=new Chart(t,c); window.__charts.push(ch); return ch;}catch(e){return null;} })(document.getElementById('chartAnomali'), {
      type:'bar',
      data:{ labels:metrics.map(m=>m.toUpperCase()), datasets:[{ label:'Jumlah Anomali', data:counts, backgroundColor:'rgba(220,38,38,0.35)', borderColor:'#dc2626' }]},
      options:{ responsive:true, plugins:{legend:{display:false}} }
  });
  // chart registered by helper
    $('#tblAnomali').DataTable({ dom:'Bfrtip', buttons:['csv','excel','pdf','print','colvis'], pageLength:25, order:[[0,'asc']] });
  })();
</script>
@endpush

@extends('layouts.app')

@section('content')
<div class="p-4">
  <h2 class="text-xl font-semibold mb-2">Quality Bias (AKP & BJR)</h2>
  @include('kpi._filters')
  <div class="mb-6">
    <div id="chartQuality" style="width:100%;height:320px" data-height="320"></div>
  </div>
  <div class="overflow-x-auto">
    <table id="tblQuality" class="min-w-full text-sm">
      <thead>
        <tr class="text-left border-b">
          <th class="py-2 pr-4">Tanggal</th>
          <th class="py-2 pr-4">Kebun</th>
          <th class="py-2 pr-4">Divisi</th>
          <th class="py-2 pr-4">AKP Panen</th>
          <th class="py-2 pr-4">AKP Calc</th>
          <th class="py-2 pr-4">Bias AKP</th>
          <th class="py-2 pr-4">BJR Hari Ini</th>
          <th class="py-2 pr-4">BJR Calc</th>
          <th class="py-2 pr-4">Bias BJR</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data as $row)
        @php
          $akp = (float)$row->akp_bias; $bjr=(float)$row->bjr_bias;
          $clsA = abs($akp) <= 0.5 ? 'text-green-600' : (abs($akp) <= 1 ? 'text-yellow-600' : 'text-red-600');
          $clsB = abs($bjr) <= 0.05 ? 'text-green-600' : (abs($bjr) <= 0.1 ? 'text-yellow-600' : 'text-red-600');
        @endphp
        <tr class="border-b">
          <td class="py-1 pr-4">{{ \Carbon\Carbon::parse($row->tanggal_panen)->format('d M Y') }}</td>
          <td class="py-1 pr-4">{{ $row->kebun }}</td>
          <td class="py-1 pr-4">{{ $row->divisi }}</td>
          <td class="py-1 pr-4">{{ number_format($row->akp_panen,2) }}%</td>
          <td class="py-1 pr-4">{{ number_format($row->akp_calc,2) }}%</td>
          <td class="py-1 pr-4 font-semibold {{ $clsA }}">{{ number_format($row->akp_bias,2) }}%</td>
          <td class="py-1 pr-4">{{ number_format($row->bjr_hari_ini,3) }}</td>
          <td class="py-1 pr-4">{{ number_format($row->bjr_calc,3) }}</td>
          <td class="py-1 pr-4 font-semibold {{ $clsB }}">{{ number_format($row->bjr_bias,3) }}</td>
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
    const rows = @json($data);
    const labels = rows.map(r => new Date(r.tanggal_panen).toLocaleDateString('id-ID'));
    const akpBias = rows.map(r => Number(r.akp_bias||0));
    const bjrBias = rows.map(r => Number(r.bjr_bias||0));
    const option = {
      tooltip: { trigger: 'axis' },
      legend: { data: ['Bias AKP (%)','Bias BJR'] },
      grid: { left: 40, right: 40, top: 30, bottom: 40 },
      xAxis: { type: 'category', data: labels },
      yAxis: { type: 'value' },
      series: [
        { name: 'Bias AKP (%)', type: 'line', smooth: true, data: akpBias, itemStyle: { color:'#ef4444' } },
        { name: 'Bias BJR', type: 'line', smooth: true, data: bjrBias, itemStyle: { color:'#3b82f6' } }
      ]
    };
    (window.__makeEChart||function(id,opt){ try{ return echarts.init(typeof id==='string'?document.getElementById(id):id).setOption(opt);}catch(e){}})('chartQuality', option);
    $('#tblQuality').DataTable({ dom:'Bfrtip', buttons:['csv','excel','pdf','print','colvis'], pageLength:25, order:[[0,'asc']] });
  })();
</script>
@endpush

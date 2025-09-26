@extends('layouts.app')

@section('content')
<div class="p-4">
  <h2 class="text-xl font-semibold mb-2">Rekonsiliasi Kebun ↔ PKS</h2>
  @include('kpi._filters')
  <div class="mb-6">
    <div id="chartRekonsiliasi" style="width:100%;height:320px" data-height="320"></div>
  </div>

  <div class="overflow-x-auto">
    <table id="tblRekonsiliasi" class="min-w-full text-sm">
      <thead>
        <tr class="text-left border-b">
          <th class="py-2 pr-4">Tanggal</th>
          <th class="py-2 pr-4">Kebun</th>
          <th class="py-2 pr-4">Divisi</th>
          <th class="py-2 pr-4">Timbang PKS (Kg)</th>
          <th class="py-2 pr-4">Timbang Kebun (Kg)</th>
          <th class="py-2 pr-4">Selisih (Kg)</th>
          <th class="py-2 pr-4">Loss %</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data as $row)
        @php
          $loss = (float)$row->loss_pct;
          $cls = $loss <= 1 ? 'text-green-600' : ($loss <= 3 ? 'text-yellow-600' : 'text-red-600');
        @endphp
        <tr class="border-b">
          <td class="py-1 pr-4">{{ \Carbon\Carbon::parse($row->tanggal_panen)->format('d M Y') }}</td>
          <td class="py-1 pr-4">{{ $row->kebun }}</td>
          <td class="py-1 pr-4">{{ $row->divisi }}</td>
          <td class="py-1 pr-4">{{ number_format($row->timbang_pks_harian,0,',','.') }}</td>
          <td class="py-1 pr-4">{{ number_format($row->timbang_kebun_harian,0,',','.') }}</td>
          <td class="py-1 pr-4">{{ number_format($row->selisih_kg,0,',','.') }}</td>
          <td class="py-1 pr-4 font-semibold {{ $cls }}">{{ number_format($row->loss_pct,2) }}%</td>
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
    const loss = rows.map(r => Number(r.loss_pct||0));
    const selisih = rows.map(r => Number(r.selisih_kg||0));
    const option = {
      tooltip: { trigger: 'axis' },
      legend: { data: ['Selisih (Kg)','Loss %'] },
      grid: { left: 40, right: 40, top: 30, bottom: 40 },
      xAxis: { type: 'category', data: labels, axisLabel: { rotate: 0 } },
      yAxis: [
        { type: 'value', name: 'Kg' },
        { type: 'value', name: '%', position: 'right' }
      ],
      series: [
        { name: 'Selisih (Kg)', type: 'bar', data: selisih, itemStyle: { color: '#3b82f6' } },
        { name: 'Loss %', type: 'line', yAxisIndex: 1, smooth: true, data: loss, itemStyle: { color: '#ef4444' } }
      ]
    };
    (window.__makeEChart||function(id,opt){ try{ return echarts.init(typeof id==='string'?document.getElementById(id):id).setOption(opt);}catch(e){}})('chartRekonsiliasi', option);
  $('#tblRekonsiliasi').DataTable({ dom:'Bfrtip', buttons:['csv','excel','pdf','print','colvis'], pageLength:25, order:[[0,'asc']] });
  })();
</script>
@endpush

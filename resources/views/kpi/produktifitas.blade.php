@extends('layouts.app')

@section('content')
<div class="p-4">
  <h2 class="text-xl font-semibold mb-2">Produktivitas Tenaga Kerja</h2>
  @include('kpi._filters')
  <div class="mb-6">
    <canvas id="chartProduktifitas" height="110"></canvas>
  </div>
  <div class="overflow-x-auto">
    <table id="tblProduktifitas" class="min-w-full text-sm">
      <thead>
        <tr class="text-left border-b">
          <th class="py-2 pr-4">Kebun</th>
          <th class="py-2 pr-4">Divisi</th>
          <th class="py-2 pr-4">Avg Output Kg/HK</th>
          <th class="py-2 pr-4">Avg Output Ha/HK</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data as $row)
        @php
          $kg = (float)$row->avg_output_kg_hk;
          $cls = $kg >= 500 ? 'text-green-600' : ($kg >= 350 ? 'text-yellow-600' : 'text-red-600');
        @endphp
        <tr class="border-b">
          <td class="py-1 pr-4">{{ $row->kebun }}</td>
          <td class="py-1 pr-4">{{ $row->divisi }}</td>
          <td class="py-1 pr-4 font-semibold {{ $cls }}">{{ number_format($row->avg_output_kg_hk,2) }}</td>
          <td class="py-1 pr-4">{{ number_format($row->avg_output_ha_hk,3) }}</td>
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
    const labels = rows.map(r => `${r.kebun} - ${r.divisi}`);
    const kg = rows.map(r => Number(r.avg_output_kg_hk||0));
    const ha = rows.map(r => Number(r.avg_output_ha_hk||0));
  window.__charts = window.__charts || [];
  const ch = new Chart(document.getElementById('chartProduktifitas'), {
      data:{ labels, datasets:[
        {type:'bar', label:'Avg Kg/HK', data:kg, backgroundColor:'rgba(22,163,74,0.35)', borderColor:'#16a34a'},
        {type:'line', label:'Avg Ha/HK', data:ha, borderColor:'#f59e0b', backgroundColor:'rgba(245,158,11,.20)'}
      ]}, options:{ responsive:true, scales:{ y:{ beginAtZero:true } } }
  });
  window.__charts.push(ch);
    $('#tblProduktifitas').DataTable({ dom:'Bfrtip', buttons:['csv','excel','pdf','print','colvis'], pageLength:25, order:[[2,'desc']] });
  })();
</script>
@endpush

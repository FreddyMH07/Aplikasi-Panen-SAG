@extends('layouts.app')

@section('content')
<div class="p-4">
  <h2 class="text-xl font-semibold mb-2">Quality Bias (AKP & BJR)</h2>
  @include('kpi._filters')
  <div class="mb-6">
    <canvas id="chartQuality" height="120"></canvas>
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
  window.__charts = window.__charts || [];
  const ch = new Chart(document.getElementById('chartQuality'), {
      data:{ labels, datasets:[
        {type:'line', label:'Bias AKP (%)', data:akpBias, borderColor:'#ef4444', backgroundColor:'rgba(239,68,68,.20)'},
        {type:'line', label:'Bias BJR', data:bjrBias, borderColor:'#3b82f6', backgroundColor:'rgba(59,130,246,.20)'}
      ]}, options:{ responsive:true, scales:{ y:{ beginAtZero:false } } }
  });
  window.__charts.push(ch);
    $('#tblQuality').DataTable({ dom:'Bfrtip', buttons:['csv','excel','pdf','print','colvis'], pageLength:25, order:[[0,'asc']] });
  })();
</script>
@endpush

@extends('layouts.app')

@section('content')
<div class="p-4">
  <h2 class="text-xl font-semibold mb-2">Budget Variance</h2>
  @include('kpi._filters')

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 p-3 rounded border dark:border-gray-700">
      <div class="text-sm mb-2">Harian: Actual vs Budget</div>
      <canvas id="chartBudgetHarian" height="140"></canvas>
    </div>
    <div class="bg-white dark:bg-gray-800 p-3 rounded border dark:border-gray-700">
      <div class="text-sm mb-2">Bulanan: Actual vs Budget</div>
      <canvas id="chartBudgetBulanan" height="140"></canvas>
    </div>
  </div>

  <h3 class="font-semibold mb-1">Harian</h3>
  <div class="overflow-x-auto mb-6">
    <table id="tblBudgetHarian" class="min-w-full text-sm">
      <thead>
        <tr class="text-left border-b">
          <th class="py-2 pr-4">Tanggal</th>
          <th class="py-2 pr-4">Kebun</th>
          <th class="py-2 pr-4">Divisi</th>
          <th class="py-2 pr-4">Actual Kg</th>
          <th class="py-2 pr-4">Budget Harian Kg</th>
          <th class="py-2 pr-4">Var Kg</th>
        </tr>
      </thead>
      <tbody>
        @foreach($harian as $row)
        @php
          $var = (float)$row->var_budget_harian_kg;
          $cls = $var >= 0 ? 'text-green-600' : 'text-red-600';
        @endphp
        <tr class="border-b">
          <td class="py-1 pr-4">{{ \Carbon\Carbon::parse($row->tanggal_panen)->format('d M Y') }}</td>
          <td class="py-1 pr-4">{{ $row->kebun }}</td>
          <td class="py-1 pr-4">{{ $row->divisi }}</td>
          <td class="py-1 pr-4">{{ number_format($row->tonase_panen_kg,0,',','.') }}</td>
          <td class="py-1 pr-4">{{ number_format($row->budget_harian,0,',','.') }}</td>
          <td class="py-1 pr-4 font-semibold {{ $cls }}">{{ number_format($row->var_budget_harian_kg,0,',','.') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <h3 class="font-semibold mb-1">Bulanan</h3>
  <div class="overflow-x-auto">
  <table id="tblBudgetBulanan" class="min-w-full text-sm">
      <thead>
        <tr class="text-left border-b">
          <th class="py-2 pr-4">Tahun</th>
          <th class="py-2 pr-4">Bulan</th>
          <th class="py-2 pr-4">Kebun</th>
          <th class="py-2 pr-4">Divisi</th>
          <th class="py-2 pr-4">Actual Kg</th>
          <th class="py-2 pr-4">Budget Kg</th>
          <th class="py-2 pr-4">Variance Kg</th>
        </tr>
      </thead>
      <tbody>
        @foreach($bulanan as $row)
        @php
          $var = (float)$row->variance_kg;
          $cls = $var >= 0 ? 'text-green-600' : 'text-red-600';
        @endphp
        <tr class="border-b">
          <td class="py-1 pr-4">{{ $row->tahun }}</td>
          <td class="py-1 pr-4">{{ $row->bulan }}</td>
          <td class="py-1 pr-4">{{ $row->kebun }}</td>
          <td class="py-1 pr-4">{{ $row->divisi }}</td>
          <td class="py-1 pr-4">{{ number_format($row->actual_kg,0,',','.') }}</td>
          <td class="py-1 pr-4">{{ number_format($row->budget_kg,0,',','.') }}</td>
          <td class="py-1 pr-4 font-semibold {{ $cls }}">{{ number_format($row->variance_kg,0,',','.') }}</td>
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
    const harian = @json($harian);
    const bulanan = @json($bulanan);
    // Charts
    const hLabels = harian.map(r => new Date(r.tanggal_panen).toLocaleDateString('id-ID'));
    const hActual = harian.map(r => Number(r.tonase_panen_kg||0));
    const hBudget = harian.map(r => Number(r.budget_harian||0));
  window.__charts = window.__charts || [];
  const ch1 = new Chart(document.getElementById('chartBudgetHarian'), {
      type:'line',
      data:{ labels: hLabels, datasets:[
        {label:'Actual Kg', data:hActual, borderColor:'#22c55e', backgroundColor:'rgba(34,197,94,.20)', tension:.2},
        {label:'Budget Kg', data:hBudget, borderColor:'#3b82f6', backgroundColor:'rgba(59,130,246,.20)', tension:.2}
      ]},
      options:{ responsive:true, interaction:{mode:'index',intersect:false}, scales:{ y:{ title:{display:true,text:'Kg'} } } }
  });
  window.__charts.push(ch1);
    const bLabels = bulanan.map(r => `${r.tahun}-${r.bulan}`);
    const bActual = bulanan.map(r => Number(r.actual_kg||0));
    const bBudget = bulanan.map(r => Number(r.budget_kg||0));
  const ch2 = new Chart(document.getElementById('chartBudgetBulanan'), {
      data:{ labels:bLabels, datasets:[
        {type:'bar', label:'Actual Kg', data:bActual, backgroundColor:'rgba(34,197,94,0.30)', borderColor:'#22c55e'},
        {type:'bar', label:'Budget Kg', data:bBudget, backgroundColor:'rgba(59,130,246,0.30)', borderColor:'#3b82f6'}
      ]},
      options:{ responsive:true, interaction:{mode:'index',intersect:false}, scales:{ y:{ title:{display:true,text:'Kg'} } } }
  });
  window.__charts.push(ch2);
    // DataTables
    $('#tblBudgetHarian').DataTable({ dom:'Bfrtip', buttons:['csv','excel','pdf','print','colvis'], pageLength:25, order:[[0,'asc']] });
    $('#tblBudgetBulanan').DataTable({ dom:'Bfrtip', buttons:['csv','excel','pdf','print','colvis'], pageLength:25, order:[[0,'asc'],[1,'asc']] });
  })();
</script>
@endpush

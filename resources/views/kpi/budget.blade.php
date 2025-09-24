@extends('layouts.app')

@section('content')
<div class="p-4">
  <h2 class="text-xl font-semibold mb-2">Budget Variance</h2>
  @include('kpi._filters')

  <h3 class="font-semibold mb-1">Harian</h3>
  <div class="overflow-x-auto mb-6">
    <table class="min-w-full text-sm">
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
    <table class="min-w-full text-sm">
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

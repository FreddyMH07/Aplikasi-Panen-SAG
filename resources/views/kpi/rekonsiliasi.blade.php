@extends('layouts.app')

@section('content')
<div class="p-4">
  <h2 class="text-xl font-semibold mb-2">Rekonsiliasi Kebun ↔ PKS</h2>
  @include('kpi._filters')

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
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

@extends('layouts.app')

@section('content')
<div class="p-4">
  <h2 class="text-xl font-semibold mb-2">Restan Tracker</h2>
  @include('kpi._filters')
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left border-b">
          <th class="py-2 pr-4">Kebun</th>
          <th class="py-2 pr-4">Divisi</th>
          <th class="py-2 pr-4">JJG Panen</th>
          <th class="py-2 pr-4">Restan JJG</th>
          <th class="py-2 pr-4">Restan %</th>
        </tr>
      </thead>
      <tbody>
        @foreach($ranking as $r)
        @php
          $rate = (float)$r->restan_rate;
          $cls = $rate <= 1 ? 'text-green-600' : ($rate <= 3 ? 'text-yellow-600' : 'text-red-600');
        @endphp
        <tr class="border-b">
          <td class="py-1 pr-4">{{ $r->kebun }}</td>
          <td class="py-1 pr-4">{{ $r->divisi }}</td>
          <td class="py-1 pr-4">{{ number_format($r->jjg_panen_jjg,0,',','.') }}</td>
          <td class="py-1 pr-4">{{ number_format($r->restant_jjg,0,',','.') }}</td>
          <td class="py-1 pr-4 font-semibold {{ $cls }}">{{ number_format($r->restan_rate,2) }}%</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

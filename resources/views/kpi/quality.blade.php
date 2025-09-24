@extends('layouts.app')

@section('content')
<div class="p-4">
  <h2 class="text-xl font-semibold mb-2">Quality Bias (AKP & BJR)</h2>
  @include('kpi._filters')
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
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

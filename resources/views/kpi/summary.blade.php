@extends('layouts.app')

@section('content')
<div class="p-4">
  <h2 class="text-xl font-semibold mb-2">Ringkasan KPI</h2>
  @include('kpi._filters')
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @php
      $loss = (float)($agg->avg_loss_pct ?? 0);
      $restan = (float)($agg->restan_rate_pct ?? 0);
      $out = (float)($agg->avg_output_kg_hk ?? 0);
      $var = (float)($agg->total_var_budget_harian_kg ?? 0);
    @endphp
    <div class="p-4 border rounded">
      <div class="text-sm text-gray-500">Loss %</div>
      <div class="text-2xl font-semibold {{ $loss <= 1 ? 'text-green-600' : ($loss <= 3 ? 'text-yellow-600' : 'text-red-600') }}">{{ number_format($loss,2) }}%</div>
    </div>
    <div class="p-4 border rounded">
      <div class="text-sm text-gray-500">Restan %</div>
      <div class="text-2xl font-semibold {{ $restan <= 1 ? 'text-green-600' : ($restan <= 3 ? 'text-yellow-600' : 'text-red-600') }}">{{ number_format($restan,2) }}%</div>
    </div>
    <div class="p-4 border rounded">
      <div class="text-sm text-gray-500">Output Kg/HK</div>
      <div class="text-2xl font-semibold {{ $out >= 500 ? 'text-green-600' : ($out >= 350 ? 'text-yellow-600' : 'text-red-600') }}">{{ number_format($out,2) }}</div>
    </div>
    <div class="p-4 border rounded">
      <div class="text-sm text-gray-500">Var Budget Harian (Kg)</div>
      <div class="text-2xl font-semibold {{ $var >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($var,0,',','.') }}</div>
    </div>
  </div>
</div>
@endsection

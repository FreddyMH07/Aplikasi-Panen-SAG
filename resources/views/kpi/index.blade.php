@extends('layouts.app')

@section('content')
<div class="p-4">
  <h1 class="text-2xl font-semibold mb-4">KPI & Analytics</h1>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <a href="{{ url('/kpi/rekonsiliasi') }}" class="p-4 border rounded hover:bg-gray-50">Rekonsiliasi Kebun ↔ PKS</a>
  <a href="{{ url('/kpi/restan') }}" class="p-4 border rounded hover:bg-gray-50">Restan Tracker</a>
  <a href="{{ url('/kpi/budget') }}" class="p-4 border rounded hover:bg-gray-50">Budget Variance</a>
  <a href="{{ url('/kpi/produktifitas') }}" class="p-4 border rounded hover:bg-gray-50">Produktivitas</a>
  <a href="{{ url('/kpi/quality') }}" class="p-4 border rounded hover:bg-gray-50">Quality Bias (AKP/BJR)</a>
  <a href="{{ url('/kpi/anomali') }}" class="p-4 border rounded hover:bg-gray-50">Anomali 3-Sigma</a>
  <a href="{{ url('/kpi/summary') }}" class="p-4 border rounded hover:bg-gray-50">Ringkasan KPI</a>
  </div>
</div>
@endsection

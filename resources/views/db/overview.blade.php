@extends('layouts.app')

@section('title','Database Overview')
@section('page-title','Database Overview')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Ringkasan Database</h2>
        <span class="px-3 py-1 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-sm">Driver: {{ $driver }}</span>
    </div>

    @if($error)
        <div class="p-4 bg-red-100 text-red-800 rounded">Error: {{ $error }}</div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2 text-left">Table</th>
                        <th class="px-4 py-2 text-right">Rows</th>
                        <th class="px-4 py-2">Columns</th>
                        <th class="px-4 py-2">Sample (max 3 rows)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tables as $t)
                        <tr class="align-top">
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">{{ $t['name'] }}</td>
                            <td class="px-4 py-2 text-right tabular-nums">{{ $t['count'] }}</td>
                            <td class="px-4 py-2">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($t['columns'] as $c)
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs text-gray-700 dark:text-gray-300">{{ $c }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                @if(count($t['sample']))
                                    <pre class="text-xs bg-gray-50 dark:bg-gray-900 p-2 rounded overflow-auto max-h-40">{{ json_encode($t['sample'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    <span class="text-gray-400 italic">(no rows)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Tidak ada tabel ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

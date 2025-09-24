@extends('layouts.app')
@section('title','Konfigurasi Kolom Tabel')
@section('page-title','Konfigurasi Kolom Tabel')
@section('content')
<div class="space-y-6">
  <div class="flex justify-between items-center">
    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Daftar Kolom</h2>
    <a href="{{ route('table-columns.create') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium"><i class="fas fa-plus mr-2"></i>Tambah Kolom</a>
  </div>
  <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
    <div class="flex flex-wrap gap-4 mb-4 items-end">
      <div>
        <label class="block text-xs font-semibold mb-1">Filter Tabel</label>
        <select id="filter_table" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
          <option value="">Semua</option>
          @foreach($tables as $t)
            <option value="{{ $t }}">{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <div class="ml-auto flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
        <span><span class="inline-block w-3 h-3 bg-green-500 rounded-sm align-middle"></span> Visible</span>
        <span><span class="inline-block w-3 h-3 bg-blue-500 rounded-sm align-middle"></span> Required</span>
      </div>
    </div>
    <table id="tableColumnsTable" class="w-full text-sm">
      <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
        <tr>
          <th class="px-3 py-2">Table</th>
          <th class="px-3 py-2">Column</th>
          <th class="px-3 py-2">Label</th>
          <th class="px-3 py-2">Type</th>
          <th class="px-3 py-2 text-center">Visible</th>
          <th class="px-3 py-2 text-center">Required</th>
          <th class="px-3 py-2 text-right">Sort</th>
          <th class="px-3 py-2 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
@endsection
@push('scripts')
<script>
let tcTable;
$(function(){
  tcTable = $('#tableColumnsTable').DataTable({
    processing:true,
    serverSide:true,
    ajax:{
      url:'{{ route('table-columns.data') }}',
      data: d => { d.table_name = $('#filter_table').val(); }
    },
    columns:[
      {data:'table_name', name:'table_name'},
      {data:'column_name', name:'column_name'},
      {data:'column_label', name:'column_label'},
      {data:'column_type', name:'column_type'},
      {data:'is_visible', name:'is_visible', className:'text-center', render:(d)=> d?'<span class="inline-block w-3 h-3 rounded-sm bg-green-500"></span>':''},
      {data:'is_required', name:'is_required', className:'text-center', render:(d)=> d?'<span class="inline-block w-3 h-3 rounded-sm bg-blue-500"></span>':''},
      {data:'sort_order', name:'sort_order', className:'text-right'},
      {data:'actions', name:'actions', orderable:false, searchable:false, className:'text-center'}
    ],
    order:[[0,'asc'],[6,'asc']]
  });
  $('#filter_table').on('change', ()=> tcTable.ajax.reload());
});
function editColumn(id){ window.location.href = `{{ route('table-columns.index') }}/${id}/edit`; }
function deleteColumn(id){
  if(!confirm('Hapus kolom ini?')) return;
  $.ajax({
    url:`{{ route('table-columns.index') }}/${id}`,
    type:'DELETE',
    data:{ _token:'{{ csrf_token() }}' },
    success: r=>{ if(r.success){ tcTable.ajax.reload(); } }
  });
}
</script>
@endpush

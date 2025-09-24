@extends('layouts.app')
@section('title','Tambah Kolom Tabel')
@section('page-title','Tambah Kolom')
@section('content')
<div class="max-w-xl mx-auto space-y-6">
  <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
    <form method="POST" action="{{ route('table-columns.store', [], false) }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium mb-1">Nama Tabel</label>
        <input name="table_name" list="tableSuggestions" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"/>
        <datalist id="tableSuggestions">
          @foreach($tableSuggestions as $t)
            <option value="{{ $t }}" />
          @endforeach
        </datalist>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Nama Kolom</label>
        <input name="column_name" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"/>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Label Kolom</label>
        <input name="column_label" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"/>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Tipe</label>
        <select name="column_type" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
          <option value="text">text</option>
          <option value="number">number</option>
          <option value="date">date</option>
          <option value="select">select</option>
          <option value="json">json</option>
        </select>
      </div>
      <div class="flex items-center space-x-4">
        <label class="inline-flex items-center space-x-2"><input type="checkbox" name="is_visible" value="1"><span>Visible</span></label>
        <label class="inline-flex items-center space-x-2"><input type="checkbox" name="is_required" value="1"><span>Required</span></label>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Sort Order</label>
        <input type="number" name="sort_order" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" min="0" value="0"/>
      </div>
      <div class="flex justify-end space-x-2 pt-4">
        <a href="{{ route('table-columns.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">Batal</a>
        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection

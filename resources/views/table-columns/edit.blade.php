@extends('layouts.app')
@section('title','Edit Kolom Tabel')
@section('page-title','Edit Kolom')
@section('content')
<div class="max-w-xl mx-auto space-y-6">
  <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
    <form method="POST" action="{{ route('table-columns.update',$column->id) }}" class="space-y-4">
      @csrf
      @method('PUT')
      <div>
        <label class="block text-sm font-medium mb-1">Nama Tabel</label>
        <input value="{{ $column->table_name }}" disabled class="w-full px-3 py-2 border rounded-lg bg-gray-100 dark:bg-gray-700 dark:border-gray-600"/>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Nama Kolom</label>
        <input value="{{ $column->column_name }}" disabled class="w-full px-3 py-2 border rounded-lg bg-gray-100 dark:bg-gray-700 dark:border-gray-600"/>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Label Kolom</label>
        <input name="column_label" value="{{ $column->column_label }}" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"/>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Tipe</label>
        <select name="column_type" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
          @foreach(['text','number','date','select','json'] as $t)
            <option value="{{ $t }}" {{ $column->column_type === $t ? 'selected' : '' }}>{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <div class="flex items-center space-x-4">
        <label class="inline-flex items-center space-x-2">
          <input type="checkbox" name="is_visible" value="1" {{ $column->is_visible ? 'checked' : '' }}><span>Visible</span>
        </label>
        <label class="inline-flex items-center space-x-2">
          <input type="checkbox" name="is_required" value="1" {{ $column->is_required ? 'checked' : '' }}><span>Required</span>
        </label>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Sort Order</label>
        <input type="number" name="sort_order" value="{{ $column->sort_order }}" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" min="0" />
      </div>
      <div class="flex justify-end space-x-2 pt-4">
        <a href="{{ route('table-columns.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">Batal</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Update</button>
      </div>
    </form>
  </div>
</div>
@endsection

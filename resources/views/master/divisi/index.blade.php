@extends('layouts.app')

@section('title', 'Master Divisi (Legacy) - Sistem Panen Sawit')
@section('page-title', 'Master Divisi (Legacy)')

@section('content')
<div class="space-y-6">
    <!-- Info Notice -->
    <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                    Legacy Feature
                </h3>
                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                    <p>Ini adalah fitur legacy untuk kompatibilitas dengan sistem lama. Untuk fitur terbaru, gunakan <a href="{{ route('master.master-data.index') }}" class="underline font-medium">Master Data</a> yang lebih lengkap.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Master Divisi (Legacy)</h2>
            <p class="text-gray-600 dark:text-gray-400">Kelola data master divisi untuk kompatibilitas sistem lama</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('master.master-data.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-right mr-2"></i>
                Gunakan Master Data Baru
            </a>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="divisiTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Nama Divisi</th>
                            <th class="px-6 py-3">Kode Divisi</th>
                            <th class="px-6 py-3">Kebun</th>
                            <th class="px-6 py-3">Luas Divisi</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi via DataTables atau static -->
                        <tr>
                            <td class="px-6 py-4">Divisi A</td>
                            <td class="px-6 py-4">DIV-A</td>
                            <td class="px-6 py-4">Kebun Sawit Utama</td>
                            <td class="px-6 py-4">125.00 Ha</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Aktif</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4">Divisi B</td>
                            <td class="px-6 py-4">DIV-B</td>
                            <td class="px-6 py-4">Kebun Sawit Utama</td>
                            <td class="px-6 py-4">125.00 Ha</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Aktif</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4">Divisi C</td>
                            <td class="px-6 py-4">DIV-C</td>
                            <td class="px-6 py-4">Kebun Sawit Selatan</td>
                            <td class="px-6 py-4">87.50 Ha</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Aktif</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4">Divisi D</td>
                            <td class="px-6 py-4">DIV-D</td>
                            <td class="px-6 py-4">Kebun Sawit Timur</td>
                            <td class="px-6 py-4">112.50 Ha</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Aktif</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable if needed
    $('#divisiTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>
@endpush

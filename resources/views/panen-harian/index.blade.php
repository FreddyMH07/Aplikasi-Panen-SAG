@extends('layouts.app')

@section('title', 'Report Panen Harian - Sistem Panen Sawit')
@section('page-title', 'Report Panen Harian')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Report Panen Harian</h2>
            <p class="text-gray-600 dark:text-gray-400">Kelola dan analisis data panen harian kelapa sawit</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('panen-harian.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Data
            </a>
            
            <button onclick="showImportModal()" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-upload mr-2"></i>
                Import Excel
            </button>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            <i class="fas fa-filter mr-2"></i>
            Filter Data
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Mulai</label>
                <input type="date" 
                       id="start_date" 
                       value="{{ date('Y-m-01') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Akhir</label>
                <input type="date" 
                       id="end_date" 
                       value="{{ date('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kebun</label>
                <select id="kebun_filter" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Semua Kebun</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Divisi</label>
                <select id="divisi_filter" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Semua Divisi</option>
                </select>
            </div>
        </div>
        
        <div class="flex justify-end space-x-2 mt-4">
            <button onclick="resetFilters()" 
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200">
                <i class="fas fa-undo mr-2"></i>
                Reset
            </button>
            <button onclick="applyFilters()" 
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>
                Filter
            </button>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="panenHarianTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Kebun</th>
                            <th class="px-6 py-3">Divisi</th>
                            <th class="px-6 py-3">AKP (%)</th>
                            <th class="px-6 py-3">TK Panen</th>
                            <th class="px-6 py-3">Luas (Ha)</th>
                            <th class="px-6 py-3">JJG Panen</th>
                            <th class="px-6 py-3">JJG Kirim</th>
                            <th class="px-6 py-3">Ketrek</th>
                            <th class="px-6 py-3">Total JJG Kirim</th>
                            <th class="px-6 py-3">Tonase (Kg)</th>
                            <th class="px-6 py-3">Refraksi (Kg)</th>
                            <th class="px-6 py-3">Refraksi (%)</th>
                            <th class="px-6 py-3">Restant JJG</th>
                            <th class="px-6 py-3">BJR Hari Ini</th>
                            <th class="px-6 py-3">Output Kg/HK</th>
                            <th class="px-6 py-3">Output Ha/HK</th>
                            <th class="px-6 py-3">Budget Harian</th>
                            <th class="px-6 py-3">Timbang Kebun</th>
                            <th class="px-6 py-3">Timbang PKS</th>
                            <th class="px-6 py-3">Rotasi Panen</th>
                            <th class="px-6 py-3">BJR Calc</th>
                            <th class="px-6 py-3">AKP Calc</th>
                            <th class="px-6 py-3">ACV Prod</th>
                            <th class="px-6 py-3">Selisih</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi via DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Custom Column Visibility Modal -->
<div id="customColumnModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-4xl mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pilih Kolom yang Ditampilkan</h3>
            <button onclick="hideCustomColumnModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="space-y-2">
                <h4 class="font-medium text-gray-900 dark:text-gray-100">Data Dasar</h4>
                <label class="flex items-center">
                    <input type="checkbox" data-column="0" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Tanggal</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="1" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Kebun</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="2" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Divisi</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="3" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">AKP (%)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="4" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">TK Panen</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="5" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Luas (Ha)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="6" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">JJG Panen</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="7" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">JJG Kirim</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="8" class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Ketrek</span>
                </label>
            </div>
            
            <div class="space-y-2">
                <h4 class="font-medium text-gray-900 dark:text-gray-100">Data Produksi</h4>
                <label class="flex items-center">
                    <input type="checkbox" data-column="9" class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Total JJG Kirim</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="10" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Tonase (Kg)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="11" class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Refraksi (Kg)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="12" class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Refraksi (%)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="13" class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Restant JJG</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="14" class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">BJR Hari Ini</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="15" class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Output Kg/HK</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="16" class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Output Ha/HK</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="17" class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Budget Harian</span>
                </label>
            </div>
            
            <div class="space-y-2">
                <h4 class="font-medium text-gray-900 dark:text-gray-100">Data Timbang & Analisis</h4>
                <label class="flex items-center">
                    <input type="checkbox" data-column="18" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Timbang Kebun</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="19" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Timbang PKS</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="20" class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Rotasi Panen</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="21" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">BJR Calc</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="22" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">AKP Calc</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="23" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">ACV Prod</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" data-column="24" checked class="mr-2 column-toggle">
                    <span class="text-sm dark:text-gray-300">Selisih</span>
                </label>
            </div>
        </div>
        
        <div class="flex justify-between">
            <div class="space-x-2">
                <button onclick="selectAllCustomColumns()" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200">
                    Pilih Semua
                </button>
                <button onclick="selectEssentialCustomColumns()" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                    Kolom Penting
                </button>
            </div>
            <div class="space-x-2">
                <button onclick="hideCustomColumnModal()" 
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <button onclick="applyCustomColumnVisibility()" 
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200">
                    Terapkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Import Data Excel</h3>
            <button onclick="hideImportModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="importForm" action="{{ route('panen-harian.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih File Excel
                </label>
                <input type="file" 
                       name="file" 
                       accept=".xlsx,.xls,.csv"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Format: Excel/CSV dengan 28 kolom sesuai template
                </p>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" 
                        onclick="hideImportModal()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                    <i class="fas fa-upload mr-2"></i>
                    Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let dataTable;

$(document).ready(function() {
    // Load kebun list
    loadKebunList();
    
    // Load divisi when kebun changes
    $('#kebun_filter').change(function() {
        const kebun = $(this).val();
        loadDivisi(kebun);
    });
    
    // Initialize DataTable with full toolbar
    dataTable = $('#panenHarianTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("panen-harian.data") }}',
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.kebun = $('#kebun_filter').val();
                d.divisi = $('#divisi_filter').val();
            }
        },
        columns: [
            { data: 'tanggal_panen', name: 'tanggal_panen', title: 'Tanggal' },
            { data: 'kebun', name: 'kebun', title: 'Kebun' },
            { data: 'divisi', name: 'divisi', title: 'Divisi' },
            { data: 'akp_panen', name: 'akp_panen', className: 'text-center', title: 'AKP (%)' },
            { data: 'jumlah_tk_panen', name: 'jumlah_tk_panen', className: 'text-right', title: 'TK Panen' },
            { data: 'luas_panen_ha', name: 'luas_panen_ha', className: 'text-right', title: 'Luas (Ha)' },
            { data: 'jjg_panen_jjg', name: 'jjg_panen_jjg', className: 'text-right', title: 'JJG Panen' },
            { data: 'jjg_kirim_jjg', name: 'jjg_kirim_jjg', className: 'text-right', title: 'JJG Kirim' },
            { data: 'ketrek', name: 'ketrek', className: 'text-center', title: 'Ketrek' },
            { data: 'total_jjg_kirim_jjg', name: 'total_jjg_kirim_jjg', className: 'text-right', title: 'Total JJG Kirim' },
            { data: 'tonase_panen_kg', name: 'tonase_panen_kg', className: 'text-right', title: 'Tonase (Kg)' },
            { data: 'refraksi_kg', name: 'refraksi_kg', className: 'text-right', title: 'Refraksi (Kg)' },
            { data: 'refraksi_persen', name: 'refraksi_persen', className: 'text-right', title: 'Refraksi (%)' },
            { data: 'restant_jjg', name: 'restant_jjg', className: 'text-right', title: 'Restant JJG' },
            { data: 'bjr_hari_ini', name: 'bjr_hari_ini', className: 'text-right', title: 'BJR Hari Ini' },
            { data: 'output_kg_hk', name: 'output_kg_hk', className: 'text-right', title: 'Output Kg/HK' },
            { data: 'output_ha_hk', name: 'output_ha_hk', className: 'text-right', title: 'Output Ha/HK' },
            { data: 'budget_harian', name: 'budget_harian', className: 'text-right', title: 'Budget Harian' },
            { data: 'timbang_kebun_harian', name: 'timbang_kebun_harian', className: 'text-right', title: 'Timbang Kebun' },
            { data: 'timbang_pks_harian', name: 'timbang_pks_harian', className: 'text-right', title: 'Timbang PKS' },
            { data: 'rotasi_panen', name: 'rotasi_panen', className: 'text-right', title: 'Rotasi Panen' },
            { data: 'bjr_calculated', name: 'bjr_calculated', className: 'text-right', title: 'BJR Calc' },
            { data: 'akp_calculated', name: 'akp_calculated', className: 'text-right', title: 'AKP Calc' },
            { data: 'acv_prod', name: 'acv_prod', className: 'text-right', title: 'ACV Prod' },
            { data: 'selisih', name: 'selisih', className: 'text-right', title: 'Selisih' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center', title: 'Aksi' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        scrollX: true,
        dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4"<"flex items-center space-x-2"B><"flex items-center space-x-2"lf>>rtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy mr-1"></i> Salin',
                className: 'bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                title: 'Data Panen Harian - ' + new Date().toLocaleDateString('id-ID')
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv mr-1"></i> CSV',
                className: 'bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                filename: 'panen-harian-' + new Date().toISOString().split('T')[0]
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                className: 'bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                filename: 'panen-harian-' + new Date().toISOString().split('T')[0],
                title: 'Data Panen Harian',
                messageTop: 'Laporan Data Panen Harian - Tanggal: ' + new Date().toLocaleDateString('id-ID')
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                className: 'bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                filename: 'panen-harian-' + new Date().toISOString().split('T')[0],
                title: 'Data Panen Harian',
                messageTop: 'Laporan Data Panen Harian - Tanggal: ' + new Date().toLocaleDateString('id-ID'),
                orientation: 'landscape',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print mr-1"></i> Cetak',
                className: 'bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                title: 'Data Panen Harian',
                messageTop: '<h3 class="text-center mb-4">Laporan Data Panen Harian</h3><p class="text-center mb-4">Tanggal Cetak: ' + new Date().toLocaleDateString('id-ID') + '</p>'
            },
            {
                text: '<i class="fas fa-columns mr-1"></i> Pilih Kolom',
                className: 'bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                action: function(e, dt, node, config) {
                    showCustomColumnModal();
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            buttons: {
                copy: 'Salin ke Clipboard',
                copyTitle: 'Salin ke Clipboard',
                copySuccess: {
                    _: '%d baris disalin',
                    1: '1 baris disalin'
                },
                colvis: 'Pilih Kolom',
                colvisRestore: 'Tampilkan Semua'
            }
        },
        rowCallback: function(row, data) {
            // Add color coding based on critical values
            
            // ACV Prod coloring
            const acvProdText = data.acv_prod || '0%';
            const acvProd = parseFloat(acvProdText.replace('%', ''));
            if (acvProd < 80) {
                $(row).find('td:eq(23)').addClass('bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 font-semibold');
            } else if (acvProd >= 80 && acvProd < 95) {
                $(row).find('td:eq(23)').addClass('bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 font-semibold');
            } else if (acvProd >= 95) {
                $(row).find('td:eq(23)').addClass('bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 font-semibold');
            }
            
            // BJR coloring (normal range 10-15 kg)
            const bjr = parseFloat(data.bjr_calculated || 0);
            if (bjr < 10 || bjr > 15) {
                $(row).find('td:eq(21)').addClass('bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 font-semibold');
            } else {
                $(row).find('td:eq(21)').addClass('bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200');
            }
            
            // Refraksi coloring (> 3% is critical)
            const refraksiText = data.refraksi_persen || '0%';
            const refraksi = parseFloat(refraksiText.replace('%', ''));
            if (refraksi > 3) {
                $(row).find('td:eq(12)').addClass('bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 font-semibold');
            } else if (refraksi > 2) {
                $(row).find('td:eq(12)').addClass('bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200');
            }
            
            // AKP coloring (normal range 0.5-1.0)
            const akp = parseFloat(data.akp_calculated || 0);
            if (akp < 0.5 || akp > 1.0) {
                $(row).find('td:eq(22)').addClass('bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200');
            }
            
            // Selisih coloring
            const selisihText = data.selisih || '0';
            const selisih = parseFloat(selisihText.replace(/[^-\d.]/g, ''));
            if (selisih < -500) {
                $(row).find('td:eq(24)').addClass('bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 font-semibold');
            } else if (selisih > 500) {
                $(row).find('td:eq(24)').addClass('bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200');
            }
        },
        initComplete: function() {
            // Style the buttons after initialization
            $('.dt-buttons').addClass('flex flex-wrap gap-2');
            $('.dt-button').addClass('inline-flex items-center');
        }
    });
    
    // Load initial data
    applyFilters();
});

function loadKebunList() {
    $.get('{{ route("api.kebun-list") }}')
        .done(function(data) {
            const kebunSelect = $('#kebun_filter');
            kebunSelect.html('<option value="">Semua Kebun</option>');
            data.forEach(function(kebun) {
                kebunSelect.append(`<option value="${kebun}">${kebun}</option>`);
            });
        });
}

function loadDivisi(kebun) {
    const divisiSelect = $('#divisi_filter');
    divisiSelect.html('<option value="">Semua Divisi</option>');
    
    if (kebun) {
        $.get(`{{ url('/api/divisi-list') }}/${kebun}`)
            .done(function(data) {
                data.forEach(function(divisi) {
                    divisiSelect.append(`<option value="${divisi}">${divisi}</option>`);
                });
            });
    }
}

function applyFilters() {
    dataTable.ajax.reload();
}

function resetFilters() {
    $('#start_date').val('{{ date("Y-m-01") }}');
    $('#end_date').val('{{ date("Y-m-d") }}');
    $('#kebun_filter').val('');
    $('#divisi_filter').val('');
    loadDivisi('');
    applyFilters();
}

function showImportModal() {
    $('#importModal').removeClass('hidden').addClass('flex');
}

function hideImportModal() {
    $('#importModal').removeClass('flex').addClass('hidden');
}

function showCustomColumnModal() {
    // Update checkboxes based on current column visibility
    $('.column-toggle').each(function() {
        const columnIndex = $(this).data('column');
        const isVisible = dataTable.column(columnIndex).visible();
        $(this).prop('checked', isVisible);
    });
    
    $('#customColumnModal').removeClass('hidden').addClass('flex');
}

function hideCustomColumnModal() {
    $('#customColumnModal').removeClass('flex').addClass('hidden');
}

function selectAllCustomColumns() {
    $('.column-toggle').prop('checked', true);
}

function selectEssentialCustomColumns() {
    // Uncheck all first
    $('.column-toggle').prop('checked', false);
    
    // Check essential columns (0-6, 10, 18-24)
    const essentialColumns = [0, 1, 2, 5, 6, 10, 18, 19, 21, 22, 23, 24];
    essentialColumns.forEach(colIndex => {
        $(`.column-toggle[data-column="${colIndex}"]`).prop('checked', true);
    });
}

function applyCustomColumnVisibility() {
    $('.column-toggle').each(function() {
        const columnIndex = $(this).data('column');
        const isVisible = $(this).is(':checked');
        dataTable.column(columnIndex).visible(isVisible);
    });
    
    hideCustomColumnModal();
    
    // Show success message
    showNotification('Pengaturan kolom berhasil diterapkan', 'success');
}

function showNotification(message, type = 'info') {
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    const notification = $(`
        <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 notification">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>${message}</span>
            </div>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

function deleteRecord(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        $.ajax({
            url: `{{ route('panen-harian.index') }}/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    dataTable.ajax.reload();
                    alert('Data berhasil dihapus');
                }
            },
            error: function() {
                alert('Gagal menghapus data');
            }
        });
    }
}
</script>
@endpush

@extends('layouts.app')

@section('title', 'Report Panen Bulanan - Sistem Panen Sawit')
@section('page-title', 'Report Panen Bulanan')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Report Panen Bulanan</h2>
            <p class="text-gray-600">Laporan agregasi data panen harian menjadi bulanan</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <button onclick="exportData()" 
                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-download mr-2"></i>
                Export Excel
            </button>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-filter mr-2"></i>
            Filter Data
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
        <label class="block text-sm font-medium text-gray-900 mb-2">Tahun</label>
        <select id="tahun_filter" 
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua Tahun</option>
                    @for($year = date('Y'); $year >= 2020; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>
            
            <div>
        <label class="block text-sm font-medium text-gray-900 mb-2">Bulan</label>
        <select id="bulan_filter" 
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua Bulan</option>
                    <option value="January">Januari</option>
                    <option value="February">Februari</option>
                    <option value="March">Maret</option>
                    <option value="April">April</option>
                    <option value="May">Mei</option>
                    <option value="June">Juni</option>
                    <option value="July">Juli</option>
                    <option value="August">Agustus</option>
                    <option value="September">September</option>
                    <option value="October">Oktober</option>
                    <option value="November">November</option>
                    <option value="December">Desember</option>
                </select>
            </div>
            
            <div>
        <label class="block text-sm font-medium text-gray-900 mb-2">Kebun</label>
        <select id="kebun_filter" 
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua Kebun</option>
                </select>
            </div>
            
            <div>
        <label class="block text-sm font-medium text-gray-900 mb-2">Divisi</label>
        <select id="divisi_filter" 
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
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
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
        <div id="tableStatus" class="hidden mb-3 p-3 rounded bg-yellow-50 border border-yellow-200 text-yellow-800"></div>
            <div class="overflow-x-auto">
        <table id="panenBulananTable" class="w-full text-sm text-left text-gray-600">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Tahun</th>
                            <th class="px-6 py-3">Bulan</th>
                            <th class="px-6 py-3">Kebun</th>
                            <th class="px-6 py-3">Divisi</th>
                            <th class="px-6 py-3">Hari Panen</th>
                            <th class="px-6 py-3">Total Luas (Ha)</th>
                            <th class="px-6 py-3">Total JJG</th>
                            <th class="px-6 py-3">Total Timbang Kebun</th>
                            <th class="px-6 py-3">Total Timbang PKS</th>
                            <th class="px-6 py-3">Total HK</th>
                            <th class="px-6 py-3">BJR Bulanan</th>
                            <th class="px-6 py-3">AKP Bulanan</th>
                            <th class="px-6 py-3">ACV Prod</th>
                            <th class="px-6 py-3">Selisih</th>
                            <th class="px-6 py-3">Selisih (%)</th>
                            <th class="px-6 py-3">Refraksi (%)</th>
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
    // Load divisi initially for all kebun
    loadDivisi('');
    
    // Initialize DataTable
    dataTable = $('#panenBulananTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("panen-bulanan.data", [], false) }}',
            data: function(d) {
                d.tahun = $('#tahun_filter').val();
                d.bulan = $('#bulan_filter').val();
                d.kebun = $('#kebun_filter').val();
                d.divisi = $('#divisi_filter').val();
            },
            error: function(xhr, error, thrown) {
                showTableStatus(`Gagal memuat data (${xhr.status} ${thrown}). Cek koneksi dan pastikan Anda sudah login.`, 'error');
            }
        },
        columns: [
            { data: 'tahun', name: 'tahun', className: 'text-center' },
            { data: 'nama_bulan_indonesia', name: 'bulan' },
            { data: 'kebun', name: 'kebun' },
            { data: 'divisi', name: 'divisi' },
            { data: 'hari_panen', name: 'hari_panen', className: 'text-center' },
            { data: 'total_luas_panen', name: 'total_luas_panen', className: 'text-right' },
            { data: 'total_jjg_panen', name: 'total_jjg_panen', className: 'text-right' },
            { data: 'total_timbang_kebun', name: 'total_timbang_kebun', className: 'text-right' },
            { data: 'total_timbang_pks', name: 'total_timbang_pks', className: 'text-right' },
            { data: 'total_jumlah_tk', name: 'total_jumlah_tk', className: 'text-right' },
            { data: 'bjr_bulanan', name: 'bjr_bulanan', className: 'text-right' },
            { data: 'akp_bulanan', name: 'akp_bulanan', className: 'text-right' },
            { data: 'acv_prod_bulanan', name: 'acv_prod_bulanan', className: 'text-right' },
            { data: 'selisih', name: 'selisih', className: 'text-right' },
            { data: 'selisih_persen', name: 'selisih_persen', className: 'text-right' },
            { data: 'refraksi_persen', name: 'refraksi_persen', className: 'text-right' }
        ],
        order: [[0, 'desc'], [1, 'asc'], [2, 'asc']],
        pageLength: 25,
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        preDrawCallback: function() { hideTableStatus(); },
        drawCallback: function(settings) {
            const info = this.api().page.info();
            if (info.recordsDisplay === 0) {
                showTableStatus('Tidak ada data untuk filter/periode yang dipilih. Coba reset filter di atas.', 'info');
            }
        }
    });
    
    // Load initial data
    applyFilters();
});

function loadKebunList() {
    $.get('{{ route("api.kebun-list", [], false) }}')
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
    $.get(`{{ route('api.divisi-list', ['kebun' => '___'], false) }}`.replace('___', encodeURIComponent(kebun)))
            .done(function(data) {
                data.forEach(function(divisi) {
                    divisiSelect.append(`<option value="${divisi}">${divisi}</option>`);
                });
            });
    } else {
        $.get(`{{ route('api.divisi-list', [], false) }}`)
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
    $('#tahun_filter').val('');
    $('#bulan_filter').val('');
    $('#kebun_filter').val('');
    $('#divisi_filter').val('');
    loadDivisi('');
    applyFilters();
}

function exportData() {
    const params = new URLSearchParams({
        tahun: $('#tahun_filter').val(),
        bulan: $('#bulan_filter').val(),
        kebun: $('#kebun_filter').val(),
        divisi: $('#divisi_filter').val()
    });
    
    window.location.href = `{{ route('panen-bulanan.export', [], false) }}?${params.toString()}`;
}

function showTableStatus(message, type = 'info') {
    const el = $('#tableStatus');
    el.removeClass('hidden');
    el.removeClass('bg-yellow-50 border-yellow-200 text-yellow-800');
    el.removeClass('bg-red-50 border-red-200 text-red-800');
    if (type === 'error') {
    el.addClass('bg-red-50 border-red-200 text-red-800');
    } else {
    el.addClass('bg-yellow-50 border-yellow-200 text-yellow-800');
    }
    el.text(message);
}

function hideTableStatus() {
    $('#tableStatus').addClass('hidden');
}
</script>
@endpush

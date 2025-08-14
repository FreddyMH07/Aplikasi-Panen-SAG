@extends('layouts.app')

@section('title', 'Master Data - Sistem Panen Sawit')
@section('page-title', 'Master Data')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Master Data</h2>
            <p class="text-gray-600 dark:text-gray-400">Kelola data master kebun, divisi, SPH, luas TM, dan budget</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('master.master-data.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Data
            </a>
            
            <button onclick="showImportModal()" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-upload mr-2"></i>
                Import Excel
            </button>
            
            <button onclick="exportData()" 
                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-download mr-2"></i>
                Export Excel
            </button>
        </div>
    </div>
    
    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <i class="fas fa-database text-green-600 dark:text-green-300"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Data</p>
                    <p id="totalData" class="text-2xl font-bold text-gray-900 dark:text-gray-100">-</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <i class="fas fa-map text-blue-600 dark:text-blue-300"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kebun Aktif</p>
                    <p id="totalKebun" class="text-2xl font-bold text-gray-900 dark:text-gray-100">-</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <i class="fas fa-sitemap text-purple-600 dark:text-purple-300"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Divisi Aktif</p>
                    <p id="totalDivisi" class="text-2xl font-bold text-gray-900 dark:text-gray-100">-</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                    <i class="fas fa-calendar text-yellow-600 dark:text-yellow-300"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tahun Aktif</p>
                    <p id="totalTahun" class="text-2xl font-bold text-gray-900 dark:text-gray-100">-</p>
                </div>
            </div>
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tahun</label>
                <select id="tahun_filter" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Semua Tahun</option>
                    @for($year = date('Y'); $year >= 2020; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bulan</label>
                <select id="bulan_filter" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kebun</label>
                <input type="text" 
                       id="kebun_filter" 
                       placeholder="Cari kebun..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <div class="flex items-end">
                <div class="flex space-x-2 w-full">
                    <button onclick="resetFilters()" 
                            class="flex-1 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </button>
                    <button onclick="applyFilters()" 
                            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="masterDataTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Kebun</th>
                            <th class="px-6 py-3">Divisi</th>
                            <th class="px-6 py-3">SPH Panen</th>
                            <th class="px-6 py-3">Luas TM</th>
                            <th class="px-6 py-3">Budget Alokasi</th>
                            <th class="px-6 py-3">PKK</th>
                            <th class="px-6 py-3">Bulan</th>
                            <th class="px-6 py-3">Tahun</th>
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

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Import Data Excel</h3>
            <button onclick="hideImportModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="importForm" action="{{ route('master.master-data.import') }}" method="POST" enctype="multipart/form-data">
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
                    Format: Kebun, Divisi, SPH Panen, Luas TM, Budget Alokasi, PKK, Bulan, Tahun
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
    // Initialize DataTable
    dataTable = $('#masterDataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("master.master-data.data") }}',
            data: function(d) {
                d.tahun = $('#tahun_filter').val();
                d.bulan = $('#bulan_filter').val();
                d.kebun = $('#kebun_filter').val();
            }
        },
        columns: [
            { data: 'kebun', name: 'kebun' },
            { data: 'divisi', name: 'divisi' },
            { data: 'sph_panen', name: 'sph_panen', className: 'text-right' },
            { data: 'luas_tm', name: 'luas_tm', className: 'text-right' },
            { data: 'budget_alokasi', name: 'budget_alokasi', className: 'text-right' },
            { data: 'pkk', name: 'pkk', className: 'text-right' },
            { data: 'nama_bulan_indonesia', name: 'bulan' },
            { data: 'tahun', name: 'tahun', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[7, 'desc'], [6, 'asc'], [0, 'asc']],
        pageLength: 25,
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            updateSummaryCards();
        }
    });
    
    // Load initial data
    applyFilters();
});

function applyFilters() {
    dataTable.ajax.reload();
}

function resetFilters() {
    $('#tahun_filter').val('');
    $('#bulan_filter').val('');
    $('#kebun_filter').val('');
    applyFilters();
}

function updateSummaryCards() {
    // This would typically make an AJAX call to get summary statistics
    // For now, we'll use placeholder values
    $('#totalData').text(dataTable.page.info().recordsTotal);
    $('#totalKebun').text('-');
    $('#totalDivisi').text('-');
    $('#totalTahun').text('-');
}

function exportData() {
    const params = new URLSearchParams({
        tahun: $('#tahun_filter').val(),
        bulan: $('#bulan_filter').val(),
        kebun: $('#kebun_filter').val()
    });
    
    window.location.href = `{{ route('master.master-data.export') }}?${params.toString()}`;
}

function showImportModal() {
    $('#importModal').removeClass('hidden').addClass('flex');
}

function hideImportModal() {
    $('#importModal').removeClass('flex').addClass('hidden');
}

function editRecord(id) {
    window.location.href = `{{ route('master.master-data.index') }}/${id}/edit`;
}

function deleteRecord(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        $.ajax({
            url: `{{ route('master.master-data.index') }}/${id}`,
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

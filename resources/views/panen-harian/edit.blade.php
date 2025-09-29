@extends('layouts.app')

@section('title', 'Edit Data Panen Harian - Sistem Panen Sawit')
@section('page-title', 'Edit Data Panen Harian')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Edit Data Panen Harian</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Perbarui data panen harian kelapa sawit</p>
                </div>
                <a href="{{ route('panen-harian.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
        
    <form action="{{ route('panen-harian.update', $panenHarian->id, false) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tanggal Panen -->
                <div>
                    <label for="tanggal_panen" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-calendar mr-1"></i>
                        Tanggal Panen <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal_panen" 
                           name="tanggal_panen" 
                           value="{{ old('tanggal_panen', $panenHarian->tanggal_panen->format('Y-m-d')) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('tanggal_panen') border-red-500 @enderror">
                    @error('tanggal_panen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Kebun (by name) -->
                <div>
                    <label for="kebun" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-map mr-1"></i>
                        Kebun <span class="text-red-500">*</span>
                    </label>
            <select id="kebun" 
                name="kebun" 
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('kebun') border-red-500 @enderror">
                        <option value="">Pilih Kebun</option>
                        @foreach($kebuns as $k)
                            <option value="{{ $k }}" {{ old('kebun', $panenHarian->kebun) == $k ? 'selected' : '' }}>
                                {{ $k }}
                            </option>
                        @endforeach
                    </select>
                    @error('kebun')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Divisi (by name) -->
                <div>
                    <label for="divisi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-sitemap mr-1"></i>
                        Divisi <span class="text-red-500">*</span>
                    </label>
            <select id="divisi" 
                name="divisi" 
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('divisi') border-red-500 @enderror">
                        <option value="">Pilih Divisi</option>
                        @foreach($divisis as $d)
                            <option value="{{ $d }}" {{ old('divisi', $panenHarian->divisi) == $d ? 'selected' : '' }}>
                                {{ $d }}
                            </option>
                        @endforeach
                    </select>
                    @error('divisi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Luas Panen (Ha) -->
                <div>
                    <label for="luas_panen_ha" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-ruler mr-1"></i>
                        Luas Panen (Ha) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="luas_panen_ha" 
                           name="luas_panen_ha" 
                           value="{{ old('luas_panen_ha', $panenHarian->luas_panen_ha) }}"
                           step="0.01"
                           min="0"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('luas_panen_ha') border-red-500 @enderror">
                    @error('luas_panen_ha')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- JJG Panen (jjg) -->
                <div>
                    <label for="jjg_panen_jjg" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-seedling mr-1"></i>
                        JJG Panen <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="jjg_panen_jjg" 
                           name="jjg_panen_jjg" 
                           value="{{ old('jjg_panen_jjg', $panenHarian->jjg_panen_jjg) }}"
                           min="0"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('jjg_panen_jjg') border-red-500 @enderror">
                    @error('jjg_panen_jjg')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Timbang Kebun (Kg) -->
                <div>
                    <label for="timbang_kebun_harian" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-weight mr-1"></i>
                        Timbang Kebun (Kg) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="timbang_kebun_harian" 
                           name="timbang_kebun_harian" 
                           value="{{ old('timbang_kebun_harian', $panenHarian->timbang_kebun_harian) }}"
                           step="0.01"
                           min="0"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('timbang_kebun_harian') border-red-500 @enderror">
                    @error('timbang_kebun_harian')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Timbang PKS (Kg) -->
                <div>
                    <label for="timbang_pks_harian" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-weight mr-1"></i>
                        Timbang PKS (Kg) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="timbang_pks_harian" 
                           name="timbang_pks_harian" 
                           value="{{ old('timbang_pks_harian', $panenHarian->timbang_pks_harian) }}"
                           step="0.01"
                           min="0"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('timbang_pks_harian') border-red-500 @enderror">
                    @error('timbang_pks_harian')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Jumlah TK (HK) -->
                <div>
                    <label for="jumlah_tk_panen" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-users mr-1"></i>
                        HK (Tenaga Kerja) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="jumlah_tk_panen" 
                           name="jumlah_tk_panen" 
                           value="{{ old('jumlah_tk_panen', $panenHarian->jumlah_tk_panen) }}"
                           min="0"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('jumlah_tk_panen') border-red-500 @enderror">
                    @error('jumlah_tk_panen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Refraksi -->
                <div>
                    <label for="refraksi_kg" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Refraksi (Kg)
                    </label>
                    <input type="number" 
                           id="refraksi_kg" 
                           name="refraksi_kg" 
                           value="{{ old('refraksi_kg', $panenHarian->refraksi_kg) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('refraksi_kg') border-red-500 @enderror">
                    @error('refraksi_kg')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Tonase Panen (Kg) -->
                <div>
                    <label for="tonase_panen_kg" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-weight-hanging mr-1"></i>
                        Tonase Panen (Kg) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="tonase_panen_kg" 
                           name="tonase_panen_kg" 
                           value="{{ old('tonase_panen_kg', $panenHarian->tonase_panen_kg) }}"
                           step="0.01"
                           min="0"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('tonase_panen_kg') border-red-500 @enderror">
                    @error('tonase_panen_kg')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Budget Harian -->
                <div>
                    <label for="budget_harian" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-dollar-sign mr-1"></i>
                        Budget Harian
                    </label>
                    <input type="number" 
                           id="budget_harian" 
                           name="budget_harian" 
                           value="{{ old('budget_harian', $panenHarian->budget_harian) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('budget_harian') border-red-500 @enderror">
                    @error('budget_harian')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Current Calculated Metrics -->
            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">Nilai Saat Ini</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-blue-700 dark:text-blue-300">BJR (Kg)</p>
                        <p class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($panenHarian->bjr, 2) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-blue-700 dark:text-blue-300">AKP</p>
                        <p class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($panenHarian->akp_calculated, 4) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-blue-700 dark:text-blue-300">ACV Prod (%)</p>
                        <p class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($panenHarian->acv_prod, 2) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-blue-700 dark:text-blue-300">Selisih (Kg)</p>
                        <p class="text-xl font-bold {{ $panenHarian->selisih >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $panenHarian->selisih >= 0 ? '+' : '' }}{{ number_format($panenHarian->selisih, 2) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Calculated Metrics Preview -->
            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Preview Perhitungan Baru</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">BJR (Kg)</p>
                        <p id="preview_bjr" class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($panenHarian->bjr, 2) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">AKP</p>
                        <p id="preview_akp" class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($panenHarian->akp_calculated, 4) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">ACV Prod (%)</p>
                        <p id="preview_acv" class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($panenHarian->acv_prod, 2) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Selisih (Kg)</p>
                        <p id="preview_selisih" class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($panenHarian->selisih, 2) }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('panen-harian.index') }}" 
                   class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load divisi when kebun changes (by kebun name)
    $('#kebun').change(function() {
        const kebun = $(this).val();
        // clear divisi first to avoid stale options
        const divisiSelect = $('#divisi');
        divisiSelect.html('<option value="">Pilih Divisi</option>');
        loadDivisiByKebun(kebun);
        calculatePreview();
    });

    // Calculate preview when inputs change
    $('#jjg_panen_jjg, #timbang_kebun_harian, #timbang_pks_harian, #luas_panen_ha, #budget_harian, #tonase_panen_kg').on('input', function() {
        calculatePreview();
    });

    // Initial calculation
    calculatePreview();
});

function loadDivisiByKebun(kebun) {
    const divisiSelect = $('#divisi');
    const selectedDivisi = divisiSelect.val();

    divisiSelect.html('<option value="">Pilih Divisi</option>');

    if (kebun) {
    $.get(`{{ url('/api/divisi-list') }}/${encodeURIComponent(kebun)}`)
            .done(function(data) {
                data.forEach(function(divisi) {
                    const selected = selectedDivisi === divisi ? 'selected' : '';
                    divisiSelect.append(`<option value="${divisi}" ${selected}>${divisi}</option>`);
                });
            })
            .fail(function() {
                console.error('Failed to load divisi data');
            });
    }
}

function calculatePreview() {
    const jjgPanen = parseFloat($('#jjg_panen_jjg').val()) || 0;
    const timbangKebun = parseFloat($('#timbang_kebun_harian').val()) || 0;
    const timbangPks = parseFloat($('#timbang_pks_harian').val()) || 0;
    const luasPanen = parseFloat($('#luas_panen_ha').val()) || 0;
    const budgetHarian = parseFloat($('#budget_harian').val()) || 0;

    // BJR = Timbang Kebun / JJG Panen
    const bjr = jjgPanen > 0 ? (timbangKebun / jjgPanen) : 0;

    // AKP = JJG Panen / (Luas Panen * SPH)
    const sph = 136; // Default SPH
    const akp = (luasPanen * sph) > 0 ? (jjgPanen / (luasPanen * sph)) : 0;

    // ACV Prod = (Timbang PKS / Budget Harian) * 100
    const acvProd = budgetHarian > 0 ? ((timbangPks / budgetHarian) * 100) : 0;

    // Selisih = Timbang PKS - Timbang Kebun
    const selisih = timbangPks - timbangKebun;

    // Update preview
    $('#preview_bjr').text(bjr.toFixed(2));
    $('#preview_akp').text(akp.toFixed(4));
    $('#preview_acv').text(acvProd.toFixed(2));
    $('#preview_selisih').text(selisih.toFixed(2)).removeClass('text-green-600 text-red-600')
        .addClass(selisih >= 0 ? 'text-green-600' : 'text-red-600');
}
</script>
@endpush

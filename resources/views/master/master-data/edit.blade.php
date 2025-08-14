@extends('layouts.app')

@section('title', 'Edit Master Data - Sistem Panen Sawit')
@section('page-title', 'Edit Master Data')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Edit Master Data</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Edit data master kebun, divisi, SPH, luas TM, dan budget</p>
                </div>
                <a href="{{ route('master.master-data.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
        
        <form action="{{ route('master.master-data.update', $masterData->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kebun -->
                <div>
                    <label for="kebun" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-map mr-1"></i>
                        Nama Kebun <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="kebun" 
                           name="kebun" 
                           value="{{ old('kebun', $masterData->kebun) }}"
                           required
                           maxlength="64"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('kebun') border-red-500 @enderror">
                    @error('kebun')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Divisi -->
                <div>
                    <label for="divisi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-sitemap mr-1"></i>
                        Nama Divisi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="divisi" 
                           name="divisi" 
                           value="{{ old('divisi', $masterData->divisi) }}"
                           required
                           maxlength="64"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('divisi') border-red-500 @enderror">
                    @error('divisi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SPH Panen -->
                <div>
                    <label for="sph_panen" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-seedling mr-1"></i>
                        SPH Panen <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="sph_panen" 
                           name="sph_panen" 
                           value="{{ old('sph_panen', $masterData->sph_panen) }}"
                           required
                           min="50"
                           max="200"
                           step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('sph_panen') border-red-500 @enderror">
                    @error('sph_panen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Standar Pokok per Hektar (50-200)</p>
                </div>

                <!-- Luas TM -->
                <div>
                    <label for="luas_tm" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-ruler mr-1"></i>
                        Luas TM (Ha) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="luas_tm" 
                           name="luas_tm" 
                           value="{{ old('luas_tm', $masterData->luas_tm) }}"
                           required
                           min="0"
                           step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('luas_tm') border-red-500 @enderror">
                    @error('luas_tm')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Luas Tanaman Menghasilkan dalam hektar</p>
                </div>

                <!-- Budget Alokasi -->
                <div>
                    <label for="budget_alokasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-dollar-sign mr-1"></i>
                        Budget Alokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="budget_alokasi" 
                           name="budget_alokasi" 
                           value="{{ old('budget_alokasi', $masterData->budget_alokasi) }}"
                           required
                           min="0"
                           step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('budget_alokasi') border-red-500 @enderror">
                    @error('budget_alokasi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Budget yang dialokasikan untuk periode ini</p>
                </div>

                <!-- PKK -->
                <div>
                    <label for="pkk" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-tree mr-1"></i>
                        PKK <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="pkk" 
                           name="pkk" 
                           value="{{ old('pkk', $masterData->pkk) }}"
                           required
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('pkk') border-red-500 @enderror">
                    @error('pkk')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pokok Kelapa Sawit (jumlah pohon)</p>
                </div>

                <!-- Bulan -->
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-calendar mr-1"></i>
                        Bulan <span class="text-red-500">*</span>
                    </label>
                    <select id="bulan" 
                            name="bulan" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('bulan') border-red-500 @enderror">
                        <option value="">Pilih Bulan</option>
                        @foreach($bulanList as $key => $value)
                            <option value="{{ $key }}" {{ old('bulan', $masterData->bulan) == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('bulan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun -->
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <select id="tahun" 
                            name="tahun" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 @error('tahun') border-red-500 @enderror">
                        <option value="">Pilih Tahun</option>
                        @for($year = date('Y') + 1; $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ old('tahun', $masterData->tahun) == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                    @error('tahun')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if($errors->has('duplicate'))
                <div class="mt-6 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800 dark:text-red-200">{{ $errors->first('duplicate') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('master.master-data.index') }}" 
                   class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Update Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-calculate PKK based on Luas TM and SPH
    $('#luas_tm, #sph_panen').on('input', function() {
        const luasTm = parseFloat($('#luas_tm').val()) || 0;
        const sphPanen = parseFloat($('#sph_panen').val()) || 136;
        const pkk = Math.round(luasTm * sphPanen);
        
        if (luasTm > 0 && sphPanen > 0) {
            $('#pkk').val(pkk);
        }
    });
});
</script>
@endpush

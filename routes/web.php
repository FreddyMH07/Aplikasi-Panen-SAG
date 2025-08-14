<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanenHarianController;
use App\Http\Controllers\PanenBulananController;
use App\Http\Controllers\KebunController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\MasterDataController;

// Authentication Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('home');
// Simple health check (no auth) for Railway
Route::get('/health', function() { return response()->json(['status' => 'ok']); });
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Panen Harian Routes
    Route::prefix('panen-harian')->name('panen-harian.')->group(function () {
        Route::get('/', [PanenHarianController::class, 'index'])->name('index');
        Route::get('/create', [PanenHarianController::class, 'create'])->name('create');
        Route::post('/', [PanenHarianController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PanenHarianController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PanenHarianController::class, 'update'])->name('update');
        Route::delete('/{id}', [PanenHarianController::class, 'destroy'])->name('destroy');
        Route::get('/export', [PanenHarianController::class, 'export'])->name('export');
        Route::post('/import', [PanenHarianController::class, 'import'])->name('import');
        Route::get('/data', [PanenHarianController::class, 'getData'])->name('data');
    });
    
    // Panen Bulanan Routes
    Route::prefix('panen-bulanan')->name('panen-bulanan.')->group(function () {
        Route::get('/', [PanenBulananController::class, 'index'])->name('index');
        Route::get('/data', [PanenBulananController::class, 'getData'])->name('data');
        Route::get('/export', [PanenBulananController::class, 'export'])->name('export');
        Route::post('/generate', [PanenBulananController::class, 'generate'])->name('generate');
    });
    
    // Master Data Routes
    Route::prefix('master')->name('master.')->group(function () {
        // Kebun (Legacy - untuk kompatibilitas)
        Route::resource('kebun', KebunController::class);
        
        // Divisi (Legacy - untuk kompatibilitas)
        Route::resource('divisi', DivisiController::class);
        
        // Master Data (New comprehensive master data)
        Route::prefix('master-data')->name('master-data.')->group(function () {
            Route::get('/', [MasterDataController::class, 'index'])->name('index');
            Route::get('/create', [MasterDataController::class, 'create'])->name('create');
            Route::post('/', [MasterDataController::class, 'store'])->name('store');
            Route::get('/{id}', [MasterDataController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [MasterDataController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MasterDataController::class, 'update'])->name('update');
            Route::delete('/{id}', [MasterDataController::class, 'destroy'])->name('destroy');
            Route::get('/data/table', [MasterDataController::class, 'getData'])->name('data');
            Route::get('/export/excel', [MasterDataController::class, 'export'])->name('export');
            Route::post('/import/excel', [MasterDataController::class, 'import'])->name('import');
        });
    });
    
    // API Routes for AJAX
    Route::prefix('api')->name('api.')->group(function () {
        // Legacy routes (untuk kompatibilitas)
        Route::get('/divisi-by-kebun/{kebun_id}', function($kebun_id) {
            return \App\Models\Divisi::where('kebun_id', $kebun_id)->where('is_active', true)->get();
        })->name('divisi-by-kebun');
        
        // New API routes untuk struktur baru
        Route::get('/divisi-by-kebun-name/{kebun}', [PanenHarianController::class, 'getDivisiByKebun'])
            ->name('divisi-by-kebun-name');
        
        Route::get('/master-data/by-kebun-divisi', [MasterDataController::class, 'getByKebunDivisi'])
            ->name('master-data.by-kebun-divisi');
        
        Route::get('/panen-harian/master-data', [PanenHarianController::class, 'getMasterData'])
            ->name('panen-harian.master-data');
        
        // Get unique kebun and divisi for filters
        Route::get('/kebun-list', function() {
            return \App\Models\PanenHarian::select('kebun')->distinct()->orderBy('kebun')->pluck('kebun');
        })->name('kebun-list');
        
        Route::get('/divisi-list/{kebun?}', function($kebun = null) {
            $query = \App\Models\PanenHarian::select('divisi')->distinct()->orderBy('divisi');
            if ($kebun) {
                $query->where('kebun', $kebun);
            }
            return $query->pluck('divisi');
        })->name('divisi-list');
    });
});

<?php

// File: routes/web.php

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BulananController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HarianController;
use App\Http\Controllers\TahunanController;
use App\Http\Controllers\LabaRugiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

Route::middleware(['auth'])->group(function () {

    /* --- RUTE DASHBOARD & LAPORAN (Hanya Pemilik) --- */
    Route::middleware('checkrole:pemilik')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/laba-rugi', [LabaRugiController::class, 'index'])->name('laba-rugi.index');
        Route::prefix('laba-rugi/export')->name('laba-rugi.export.')->group(function () {
            Route::get('/pdf', [LabaRugiController::class, 'exportPdf'])->name('pdf');
            Route::get('/excel', [LabaRugiController::class, 'exportExcel'])->name('excel');
        });
        Route::get('/harian', [HarianController::class, 'index'])->name('rekap.harian');
        Route::get('/rekap-harian/grafik/{year?}/{month?}', [HarianController::class, 'showGraph'])->name('rekap.harian.grafik');
        Route::get('/bulanan', [BulananController::class, 'index'])->name('rekap.bulanan');
        Route::get('/rekap-bulanan/grafik/{year?}', [BulananController::class, 'showGraph'])->name('rekap.bulanan.grafik');
        Route::get('/tahunan', [TahunanController::class, 'index'])->name('rekap.tahunan');
        Route::get('/rekap-tahunan/grafik/{year?}', [TahunanController::class, 'showGraph'])->name('rekap.tahunan.grafik');

        // Pemilik hanya melihat daftar (View Only)
        Route::get('/pemilik/daftar-pengeluaran', [ExpenseController::class, 'ownerIndex'])->name('pemilik.pengeluaran.index');
    });

    /* --- RUTE INPUT TRANSAKSI & MASTER DATA (Hanya Admin) --- */
    Route::middleware('checkrole:admin')->group(function () {
        Route::get('/input-transaksi', [TransactionController::class, 'create'])->name('input.transaksi');
        Route::post('/transaksi/store', [TransactionController::class, 'store'])->name('transactions.store');

        Route::resource('menu', MenuController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::prefix('master-kategori')->name('master.kategori.')->group(function () {
            Route::get('/', [MenuController::class, 'categoryIndex'])->name('index');
            Route::post('/', [MenuController::class, 'categoryStore'])->name('store');
            Route::put('/{category}', [MenuController::class, 'categoryUpdate'])->name('update');
            Route::delete('/{category}', [MenuController::class, 'categoryDestroy'])->name('destroy');
        });

        Route::post('/admin/master-item', [ExpenseController::class, 'storeMasterItem'])->name('admin.master-item.store');

        // REVISI: Mengembalikan rute Update dan Delete ke Admin
        Route::prefix('admin/kelola-pengeluaran')->name('admin.pengeluaran.')->group(function () {
            Route::get('/', [ExpenseController::class, 'adminIndex'])->name('index');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
            Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
            Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        });
    });
});

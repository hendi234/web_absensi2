<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsenController;

Route::get('/', function () {
    return redirect("/absensi/login");
});

Route::get('/absenkeluar', function () {
    return view('absensi.keluar', [
        'user' => Filament::auth()->user(), // Ambil user dari Filament
    ]);
})->name('absen-keluar');

Route::middleware('filament.auth')->group(function () {
    Route::get('/absenmasuk', function () {
        return view('absensi.masuk', [
            'user' => Filament::auth()->user(), // Ambil user dari Filament
        ]);
    })->name('absen-masuk');
});


// route form untuk absensi
Route::get('/absenmasuk', [AbsenController::class, 'create'])->name('absenmasuk.create');
Route::get('/absenmasuk', [AbsenController::class, 'user'])->name('absenmasuk.user');
Route::post('/absenmasuk', [AbsenController::class, 'absenMasuk'])->name('absenmasuk.absenMasuk');
Route::post('/absenkeluar', [AbsenController::class, 'absenKeluar'])->name('absenkeluar.absenKeluar');

// route untuk get data absensi harian
Route::get('/absensi-harian', [AbsenController::class, 'getAbsensiHarian'])->name('absensi.harian');



<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard.index')->name('dashboard');
    Route::view('/barang', 'barang.index')->name('barang.index');
    Route::patch('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::view('/log', 'log.index')->name('log.index');
    Route::view('/scan', 'scan.index')->name('scan.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

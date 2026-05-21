<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RFIDController;
use Illuminate\Support\Facades\Route;

Route::get('/barang', [BarangController::class, 'index']);
Route::post('/barang', [BarangController::class, 'store']);
Route::patch('/barang/{barang}', [BarangController::class, 'update']);
Route::get('/log', [LogController::class, 'index']);
Route::post('/rfid/scan', [RFIDController::class, 'scan'])->middleware('rfid.key');

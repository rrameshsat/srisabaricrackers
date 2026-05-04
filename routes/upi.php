<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UpiConfigController;

Route::prefix('upi')->group(function () {
    Route::get('/config', [UpiConfigController::class, 'index'])->name('upi.config.index');
    Route::post('/config', [UpiConfigController::class, 'store'])->name('upi.config.update');
});

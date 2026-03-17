<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LuckyDrawController;

Route::get('/', [LuckyDrawController::class, 'index']);
Route::get('/api/current-prize', [LuckyDrawController::class, 'getCurrentPrize']);
Route::post('/api/draw', [LuckyDrawController::class, 'draw']);
Route::get('/api/winners', [LuckyDrawController::class, 'getWinners']);
Route::get('/api/winners-all', [LuckyDrawController::class, 'getAllWinners']);
Route::get('/api/stats', [LuckyDrawController::class, 'getStats']);
Route::get('/admin', [LuckyDrawController::class, 'admin']);
Route::post('/admin/prizes', [LuckyDrawController::class, 'storePrize']);
Route::delete('/admin/prizes/{prize}', [LuckyDrawController::class, 'deletePrize']);

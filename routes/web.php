<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LuckyDrawController;

Route::get('/', [LuckyDrawController::class, 'index']);
Route::get('/api/current-prize', [LuckyDrawController::class, 'getCurrentPrize']);
Route::post('/api/draw', [LuckyDrawController::class, 'draw']);
Route::post('/api/draw-all', [LuckyDrawController::class, 'drawAll']);
Route::get('/api/winners', [LuckyDrawController::class, 'getWinners']);
Route::get('/api/winners-all', [LuckyDrawController::class, 'getAllWinners']);
Route::get('/api/stats', [LuckyDrawController::class, 'getStats']);

Route::get('/admin', [LuckyDrawController::class, 'admin']);

Route::get('/admin/draws', [LuckyDrawController::class, 'drawsIndex']);
Route::get('/admin/draws/{draw}', [LuckyDrawController::class, 'showDraw']);
Route::put('/admin/draws/{draw}', [LuckyDrawController::class, 'updateDraw']);
Route::post('/admin/draws', [LuckyDrawController::class, 'storeDraw']);
Route::post('/admin/draws/{draw}/activate', [LuckyDrawController::class, 'activateDraw']);
Route::post('/admin/draws/{draw}/prizes', [LuckyDrawController::class, 'storePrizeForDraw']);
Route::post('/admin/draws/{draw}/employees', [LuckyDrawController::class, 'storeEmployee']);
Route::delete('/admin/employees/{employee}', [LuckyDrawController::class, 'deleteEmployee']);
Route::delete('/admin/draws/{draw}', [LuckyDrawController::class, 'deleteDraw']);

Route::get('/admin/prizes', [LuckyDrawController::class, 'prizesIndex']);
Route::post('/admin/prizes', [LuckyDrawController::class, 'storePrize']);
Route::get('/admin/prizes/{prize}', [LuckyDrawController::class, 'showPrize']);
Route::put('/admin/prizes/{prize}', [LuckyDrawController::class, 'updatePrize']);
Route::delete('/admin/prizes/{prize}', [LuckyDrawController::class, 'deletePrize']);

Route::get('/admin/employees', [LuckyDrawController::class, 'employeesIndex']);
Route::post('/admin/employees', [LuckyDrawController::class, 'storeEmployeeGeneral']);
Route::post('/admin/employees/import', [LuckyDrawController::class, 'importEmployees']);
Route::delete('/admin/employees/{employee}', [LuckyDrawController::class, 'deleteEmployee']);

Route::get('/admin/winners', [LuckyDrawController::class, 'winnersIndex']);

Route::put('/api/winners/{winner}', [LuckyDrawController::class, 'updateWinner']);
Route::post('/api/winners/restore', [LuckyDrawController::class, 'restoreWinner']);
Route::delete('/api/winners/{winner}', [LuckyDrawController::class, 'deleteWinner']);

Route::post('/admin/prizes/import', [LuckyDrawController::class, 'importPrizes']);

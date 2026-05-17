<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AiController;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/tco-calculator', [ProductController::class, 'tco'])->name('tco');
Route::get('/watchlist', [ProductController::class, 'watchlist'])->name('watchlist');
Route::post('/watchlist/add', [ProductController::class, 'addWatchlist'])->name('watchlist.add');
Route::post('/watchlist/remove', [ProductController::class, 'removeWatchlist'])->name('watchlist.remove');
Route::get('/cs-report', [ProductController::class, 'csReport'])->name('cs.report');
Route::get('/cs-report/export', [ProductController::class, 'exportCS'])->name('cs.export');
Route::get('/anomalies', [ProductController::class, 'anomalies'])->name('anomalies');

// AI Features Routes
Route::get('/ai/matcher', [AiController::class, 'matcher'])->name('ai.matcher');
Route::get('/ai/rfp', [AiController::class, 'rfp'])->name('ai.rfp');
Route::match(['get', 'post'], '/ai/ocr', [AiController::class, 'ocr'])->name('ai.ocr');

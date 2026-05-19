<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\AuthController;

// Public Pages
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/tco-calculator', [ProductController::class, 'tco'])->name('tco');
Route::get('/anomalies', [ProductController::class, 'anomalies'])->name('anomalies');
Route::get('/dashboard', [ProductController::class, 'dashboard'])->name('dashboard');

// Watchlist
Route::get('/watchlist', [ProductController::class, 'watchlist'])->name('watchlist');
Route::post('/watchlist/add', [ProductController::class, 'addWatchlist'])->name('watchlist.add');
Route::post('/watchlist/remove', [ProductController::class, 'removeWatchlist'])->name('watchlist.remove');

// CS Reports
Route::get('/cs-report', [ProductController::class, 'csReport'])->name('cs.report');
Route::get('/cs-report/export', [ProductController::class, 'exportCS'])->name('cs.export');

// AI Features
Route::get('/ai/matcher', [AiController::class, 'matcher'])->name('ai.matcher');
Route::get('/ai/rfp', [AiController::class, 'rfp'])->name('ai.rfp');
Route::match(['get', 'post'], '/ai/ocr', [AiController::class, 'ocr'])->name('ai.ocr');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

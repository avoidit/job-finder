<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/postings/{posting}/status', [DashboardController::class, 'setStatus'])->name('postings.status');

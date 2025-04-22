<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;

Route::get('/{locale?}', [HomeController::class, 'index'])
    ->where('locale', 'de|en')
    ->name('home');

// Group all localized routes
Route::prefix('{locale}')
    ->where(['locale' => 'de|en'])
    ->group(function () {
        Route::get('/projekte', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projekte/{slug}', [ProjectController::class, 'show'])->name('projects.show');
    });
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\NewsArchiveController;

Route::get('/{locale?}', [HomeController::class, 'index'])
    ->where('locale', 'de|en')
    ->name('home');

Route::prefix('{locale}')
    ->where(['locale' => 'de|en'])
    ->group(function () {
        Route::get('/projekte', [ProjectController::class, 'index'])
            ->name('projects.index');

        Route::get('/projekte/{slug}', [ProjectController::class, 'show'])
            ->name('projects.show');

        Route::get('/news-archiv', [NewsArchiveController::class, 'index'])
            ->name('news.archive');
    });

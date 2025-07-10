<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RowController;
use App\Http\Controllers\NewsArchiveController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InfoController;

Route::get('/{locale?}', [HomeController::class, 'index'])
    ->where('locale', 'de|en')
    ->name('home');

Route::prefix('{locale}')
    ->where(['locale' => 'de|en'])
    ->group(function () {
        Route::get('/projekte', [ProjectController::class, 'index'])
            ->name('projects.index');

        Route::get('projekte/{slug}/{tabIndex?}', [ProjectController::class, 'show'])
            ->name('projects.show');

        Route::get('/news-archiv', [NewsArchiveController::class, 'index'])
            ->name('news.archive');

        Route::get('/reihen/{slug}/{tabIndex?}', [RowController::class, 'show'])
            ->name('rows.show');

        Route::get('ueber-uns/{tabSlug?}', [InfoController::class, 'index'])
            ->name('about');

        Route::get('/image/{uuid}', [ImageController::class, '__invoke'])
            ->where('uuid', '[0-9a-f\-]+')
            ->name('image.show');

    });

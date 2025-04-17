<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/{locale?}', [HomeController::class, 'index'])
    ->where('locale', 'de|en')
    ->name('home');
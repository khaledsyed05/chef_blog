<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;

Route::prefix('home')->group(function () {
    Route::get('/', [HomeController::class, 'getHomePageData']);
});
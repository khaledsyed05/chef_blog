<?php

use App\Http\Controllers\API\LikeableController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\RecipeController;

Route::prefix('home')->group(function () {
    Route::get('/', [HomeController::class, 'getHomePageData']);
});

Route::prefix('recipes')->group(function (){
    Route::get('/{recipe}', [RecipeController::class, 'show']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('{type}/{id}/toggle-like', [LikeableController::class, 'toggleLike']);
    Route::delete('{type}/{id}/unlike', [LikeableController::class, 'unlike']);
});
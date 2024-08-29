<?php

use App\Http\Controllers\API\CommentableController;
use App\Http\Controllers\API\LikeableController;
use App\Http\Controllers\Front\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\RecipeController;

Route::prefix('home')->group(function () {
    Route::get('/search', [HomeController::class, 'search']);
    Route::get('/featured-recipes', [HomeController::class, 'featuredRecipes']);
    Route::get('/published-recipes', [HomeController::class, 'publishedRecipes']);
    Route::get('/published-articles', [HomeController::class, 'publishedArticles']);
});

Route::prefix('recipes')->group(function (){
    Route::get('/{recipe}', [RecipeController::class, 'show']);
});

Route::prefix('articles')->group(function (){
    Route::get('/{article}', [ArticleController::class, 'show']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('{type}/{id}/likes', [LikeableController::class, 'index']);
    Route::post('{type}/{id}/toggle-like', [LikeableController::class, 'toggleLike']);
    Route::delete('{type}/{id}/unlike', [LikeableController::class, 'unlike']);

    Route::get('{type}/{id}/comments', [CommentableController::class, 'index']);
    Route::post('{type}/{id}/comments', [CommentableController::class, 'store']);
    Route::put('{type}/{id}/comments/{comment}', [CommentableController::class, 'update']);
    Route::delete('{type}/{id}/comments/{comment}', [CommentableController::class, 'destroy']);
});
<?php

use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\Dashboard\ArticleController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\RecipeController;
use App\Http\Controllers\Dashboard\TagController;
use App\Http\Controllers\Dashboard\LanguageController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Models\Tag;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware'  =>   ['auth:api', 'verified', 'auth.type:super-admin,admin'],
    'as'          =>   'dashboard.',
    'prefix'      =>   'dashboard'
], function () {
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings', [SettingsController::class, 'update']);
    Route::get('/settings/{key}', [SettingsController::class, 'show']);


    Route::get('/tags/trashed', [TagController::class, 'trashed']);
    Route::post('tags/{id}/restore', [TagController::class, 'restore']);
    Route::delete('tags/{id}/force-delete', [TagController::class, 'forceDelete']);
    Route::apiResource('/tags', TagController::class);



    Route::get('/categories/trashed', [CategoryController::class, 'trashed']);
    Route::post('categories/{id}/restore', [CategoryController::class, 'restore']);
    Route::delete('categories/{id}/force-delete', [CategoryController::class, 'forceDelete']);
    Route::apiResource('/categories', CategoryController::class);


    Route::get('/recipes/trashed', [RecipeController::class, 'trashed']);
    Route::post('recipes/{id}/restore', [RecipeController::class, 'restore']);
    Route::delete('recipes/{id}/force-delete', [RecipeController::class, 'forceDelete']);
    Route::patch('recipes/{recipe}/toggle-feature', [RecipeController::class, 'toggleFeature']);
    Route::patch('recipes/{recipe}/toggle-published', [RecipeController::class, 'togglePublished']);
    Route::apiResource('/recipes', RecipeController::class);


    Route::get('articles/trashed', [ArticleController::class, 'trashed']);
    Route::post('articles/{id}/restore', [ArticleController::class, 'restore']);
    Route::delete('articles/{id}/force-delete', [ArticleController::class, 'forceDelete']);
    Route::patch('articles/{article}/toggle-feature', [ArticleController::class, 'toggleFeature']);
    Route::patch('articles/{article}/toggle-published', [ArticleController::class, 'togglePublished']);
    Route::apiResource('articles', ArticleController::class);

    Route::apiResource('languages', LanguageController::class);
    Route::patch('languages/{language}/toggle-active', [LanguageController::class, 'toggleActive']);
});

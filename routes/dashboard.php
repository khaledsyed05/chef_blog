<?php


use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecipesController;
use App\Http\Controllers\TagsController;

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.dashboard')->middleware('auth.type:admin,super-admin');
Route::group([
    'middleware'  =>   ['auth', 'verified','auth.type:super-admin,admin'],
    'as'          =>   'dashboard.',
    'prefix'      =>   'dashboard'
],function () {
    
    
    //Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/categories', CategoriesController::class);
    Route::resource('/articles', ArticlesController::class);
    Route::resource('/tags', TagsController::class);
    Route::resource('/recipes', RecipesController::class);
    Route::resource('/comments', CommentsController::class);

});
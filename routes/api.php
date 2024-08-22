<?php

use App\Http\Controllers\API\LikeableController;
use App\Http\Controllers\Dashboard\SettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:api')->group(function () {
    Route::post('{type}/{id}/toggle-like', [LikeableController::class, 'toggleLike']);
    Route::delete('{type}/{id}/unlike', [LikeableController::class, 'unlike']);
});
// Route::post('/login', [AuthApiController::class, 'login']);
// Route::middleware('auth:api')->post('/logout', [AuthApiController::class, 'logout']);

require __DIR__.'\DashboardApis.php';

require __DIR__.'\AuthApis.php';




Route::get('/test-locale', function () {
        // Log::info('Test route called');
        // Log::info('Current app.locale:', [config('app.locale')]);
    return response()->json([
        'app_locale' => config('app.locale'),
        'app_locales' => config('app.locales'),
        ]);
});

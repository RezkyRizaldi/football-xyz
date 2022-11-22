<?php

use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('teams/{team}', [TeamController::class, 'update'])->name('teams.update')->withTrashed();
Route::get('/game-report', ReportController::class)->withTrashed();
Route::apiResource('teams', TeamController::class)->except(['update'])->withTrashed();
Route::apiResource('players', PlayerController::class)->withTrashed();
Route::apiResource('games', GameController::class)->withTrashed();

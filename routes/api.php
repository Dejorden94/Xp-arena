<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TaskController;



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

Route::post('/tasks/{taskId}/complete', [TaskController::class, 'completeTask']);
Route::get('/tasks/unverified', [TaskController::class, 'getUnverifiedTasks']);
Route::put('/tasks/{taskId}/verify', [TaskController::class, 'verifyTask']);


Route::get('/user/{id}', [UserController::class, 'getUserById']);

Route::get('/games/{gameId}', [GameController::class, 'show']);

Route::match(['get', 'post'], '/games/{gameId}/tasks', [GameController::class, 'handleTasks']);

Route::delete('/games/{gameId}/tasks/{taskId}', [GameController::class, 'deleteTask']);

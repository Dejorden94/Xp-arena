<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TaskController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::get('/games', [GameController::class, 'index']);
Route::post('/games', [GameController::class, 'store']);

Route::post('/games/{gameId}/tasks/{taskId}/completeTask', [TaskController::class, 'completeTask']);
Route::post('/tasks/{taskId}/complete', [TaskController::class, 'completeTask']);
Route::get('/tasks/unverified', [TaskController::class, 'getUnverifiedTasks']);
Route::put('/tasks/{taskId}/verify', [TaskController::class, 'verifyTask']);


Route::post('/tasks/{taskId}/reject', [TaskController::class, 'rejectTask'])->middleware('auth');

Route::post('/games/follow', [GameController::class, 'followGame']);
Route::get('/dashboard/games', [GameController::class, 'followedGames'])->middleware('auth');
Route::get('/users/{userId}/games/{gameId}/followed-tasks', [GameController::class, 'getFollowedGameTasks']);

Route::post('/games/{gameId}/add-task', [TaskController::class, 'addTask'])->middleware('auth');



Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/users/{user}/games', [GameController::class, 'index']);
});


require __DIR__ . '/auth.php';

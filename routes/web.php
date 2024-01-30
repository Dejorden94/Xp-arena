<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CheckpointController;
use App\Http\Controllers\UserController;
use App\Models\Checkpoint;

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

Route::get('/about', function () {
    return Inertia::render('About', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('about');

Route::get('user/{userId}/checkLevel', [TaskController::class, 'updateUserLevel']);

Route::get('/games', [GameController::class, 'index']);
Route::post('/games', [GameController::class, 'store']);
Route::post('/game-image-upload', [GameController::class, 'uploadGameImage']);

Route::post('/tasks/{taskId}/completeTask', [TaskController::class, 'completeTask']);
Route::get('/unverified-tasks', [TaskController::class, 'getUnverifiedTasks']);
Route::put('/follower-tasks/{taskId}', [TaskController::class, 'acceptFollowerTask']);
Route::put('/follower-tasks/decline/{id}', [TaskController::class, 'updateStatus']);

Route::post('/games/follow', [GameController::class, 'followGame']);
Route::get('/dashboard/games', [GameController::class, 'followedGames'])->middleware('auth');
Route::get('/users/{userId}/games/{gameId}/followed-tasks', [GameController::class, 'getFollowedGameTasks']);
Route::get('/users/{userId}', [UserController::class, 'getUserById']);

Route::post('/games/{gameId}/add-task', [TaskController::class, 'addTask'])->middleware('auth');

// Criteria Routes 
Route::post('/task/{taskId}/add-criteria', [TaskController::class, 'addCriteria']);
Route::put('/criteria/{criterionId}/edit', [TaskController::class, 'editCriteria']);
Route::delete('/criteria/{criterionId}/delete', [TaskController::class, 'deleteCriteria']);
Route::get('/task/{taskId}/check-criteria', [TaskController::class, 'checkCriteria']);
// Toggle the is_met value for a criterion
Route::post('/task/{taskId}/criterion/{criterionId}/toggle-met', [TaskController::class, 'toggleCriterionMet']);
Route::post('/follower-task/{taskId}/criterion/{criterionId}/toggle-met', [TaskController::class, 'toggleFollowerCriterionMet']);

Route::get('/task/{taskId}', [TaskController::class, 'getTaskDescription']);
Route::put('/task/{taskId}', [TaskController::class, 'updateTask']);
Route::put('/tasks/{taskId}/update-experience', [TaskController::class, 'updateTaskExperience']);


Route::get('/follower-tasks/{followerTaskId}/details', [TaskController::class, 'getFollowerTaskCriteria']);


// Checkpoint Routes
Route::resource('checkpoints', CheckpointController::class);
Route::post('tasks/{task}/complete', [TaskController::class, 'complete']);
Route::post('/checkpoints', [CheckpointController::class, 'store']);
Route::post('/checkpoints/{checkpointId}/add-task', [CheckpointController::class, 'addTaskToCheckpoint']);

Route::get('/follower-task/{followerTaskId}/check-criteria', [TaskController::class, 'checkFollowerCriteria']);
// Route om alle checkpoints voor een specifieke game op te halen
Route::get('/games/{game}/checkpoints', [CheckpointController::class, 'index']);

Route::patch('/checkpoints/{checkpointId}/update-order', [CheckpointController::class, 'updateOrder']);

// Route om een taak toe te wijzen aan een checkpoint
Route::post('/checkpoints/{checkpoint}/assign-task', [CheckpointController::class, 'assignTask']);


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/users/{user}/games', [GameController::class, 'index']);
});


require __DIR__ . '/auth.php';

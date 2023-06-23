<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Game;
use App\Models\User;
use App\Models\CompletedTask;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function completeTask($taskId)
    {
        // Controleer of de taak bestaat
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Controleer of de gebruiker de maker van het spel is
        $game = $task->game;

        if (Auth::id() === $game->user_id) {
            return response()->json(['error' => 'Cannot complete your own task'], 400);
        }

        // Controleer of de taak al is voltooid door de gebruiker
        $completedTask = CompletedTask::where('task_id', $taskId)->where('user_id', Auth::id())->first();

        if ($completedTask) {
            return response()->json(['error' => 'Task already completed'], 400);
        }


        // Markeer de taak als voltooid
        $completedTask = new CompletedTask;
        $completedTask->task_id = $taskId;
        $completedTask->user_id = Auth::id();
        $completedTask->game_id = $game->id;
        $completedTask->creator_id = $game->user_id;  // stel de creator_id in
        $completedTask->is_verified = false; // Je kan deze regel negeren als je geen verificatieproces nodig hebt
        $completedTask->save();

        return response()->json(['message' => 'Task marked as complete']);
    }

    public function getUnverifiedTasks()
    {
        // Controleer of de gebruiker een spel heeft gemaakt
        $game = Game::where('user_id', Auth::id())->first();

        if (!$game) {
            // De gebruiker heeft geen spel. Retourneer een succesvolle respons in plaats van een fout.
            return response()->json(['message' => 'No game found for this user'], 200);
        }

        // Haal de taken op die nog niet zijn geverifieerd
        $tasks = CompletedTask::where('creator_id', Auth::id())
            ->where('is_verified', false)
            ->get();


        return response()->json($tasks);
    }




    public function verifyTask($taskId)
    {
        // Controleer of de taak bestaat
        $completedTask = CompletedTask::find($taskId);

        if (!$completedTask) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Controleer of de taak behoort tot een spel van de gebruiker
        $game = Game::where('user_id', Auth::id())->where('id', $completedTask->game_id)->first();

        if (!$game) {
            return response()->json(['error' => 'Task does not belong to a game of the user'], 400);
        }

        // Markeer de taak als geverifieerd
        $completedTask->is_verified = true;
        $completedTask->save();

        // Zoek de gebruiker die de taak heeft voltooid
        $user = User::find($completedTask->user_id);

        // Zoek de ervaringspunten van de taak
        $task = Task::find($completedTask->task_id);
        $experiencePoints = $task->experience;

        // Verhoog de ervaringspunten van de gebruiker
        $user->experience += $experiencePoints;
        $user->save();

        return response()->json(['message' => 'Task verified and experience points added']);
    }
    public function rejectTask($taskId)
    {
        // Controleer of de taak bestaat
        $completedTask = CompletedTask::find($taskId);

        if (!$completedTask) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Controleer of de taak behoort tot een spel van de gebruiker
        $game = Game::where('user_id', Auth::id())->where('id', $completedTask->game_id)->first();

        if (!$game) {
            return response()->json(['error' => 'Task does not belong to a game of the user'], 400);
        }

        $task = Task::findOrFail($taskId);
        $task->is_verified = false;
        $task->is_rejected = true;
        $task->save();

        // Stuur de bijgewerkte taak terug in de response
        return response()->json(['task' => $task], 200);
    }
}

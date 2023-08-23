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


        // Markeer de taak als voltooid
        $completedTask = new CompletedTask;
        $completedTask->task_id = $taskId;
        $completedTask->user_id = Auth::id();
        $completedTask->game_id = $game->id;
        $completedTask->creator_id = $game->user_id;  // stel de creator_id in
        $completedTask->is_verified = false; // Je kan deze regel negeren als je geen verificatieproces nodig hebt
        $completedTask->completion_status = true; // Markeren als voltooid
        $completedTask->save();

        return response()->json(['message' => 'Task marked as complete']);
    }

    public function getUnverifiedTasks()
    {
        // Controleer of de gebruiker een spel heeft gemaakt
        $game = Game::where('user_id', Auth::id())->first();

        if (!$game) {
            // De gebruiker heeft geen spel. Retourneer een lege array als er geen taken zijn om te laten zien.
            return response()->json([]);
        }

        // Haal de taken op die nog niet zijn geverifieerd en behoren tot het spel van de gebruiker
        $tasks = CompletedTask::where('creator_id', Auth::id())
            ->where('is_verified', false)
            ->where('game_id', $game->id)
            ->get();

        return response()->json($tasks);
    }


    public function resubmitTask($taskId)
    {
        // Controleer of de taak bestaat
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Controleer of de gebruiker de volger van het spel is
        $game = $task->game;

        if (Auth::id() !== $game->follower_id) {
            return response()->json(['error' => 'Cannot resubmit a task of a game you are not following'], 400);
        }

        // Controleer of de taak is afgewezen
        if (!$task->is_rejected) {
            return response()->json(['error' => 'Cannot resubmit a task that has not been rejected'], 400);
        }

        // Zet de taak terug naar 'unverified' en 'not rejected'
        $task->is_verified = false;
        $task->is_rejected = false;
        $task->save();

        return response()->json(['message' => 'Task resubmitted for approval']);
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

        // Verwijder de voltooide taak uit de database
        $completedTask->delete();

        return response()->json(['message' => 'Task verified and experience points added']);
    }

    public function rejectTask($taskId)
    {
        // Zoek de taak
        $completedTask = CompletedTask::where('id', $taskId)
            ->whereHas('game', function ($query) {
                $query->where('creator_id', Auth::id());
            })
            ->first();

        if (!$completedTask) {
            return response()->json(['error' => 'Task not found or not related to your game'], 400);
        }

        // Markeer de taak als afgewezen
        $completedTask->is_verified = false;
        $completedTask->is_rejected = true;
        $completedTask->save();

        return response()->json(['message' => 'Task rejected']);
    }

    public function checkTaskCompleted($gameId, $taskId)
    {
        $userId = Auth::id();

        $completedTask = CompletedTask::where('game_id', $gameId)
            ->where('task_id', $taskId)
            ->where('user_id', $userId)
            ->exists();

        return response()->json(['completed' => $completedTask]);
    }
}

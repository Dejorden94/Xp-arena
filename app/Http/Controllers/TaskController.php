<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Game;
use App\Models\User;
use App\Models\FollowerTask;
use App\Models\CompletedTask;
use Illuminate\Support\Facades\Auth;


class TaskController extends Controller
{
    public function completeTask($taskId)
    {
        //Zoek de huidige user
        $user = Auth::user();

        // Zoek de taak
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Zoek de game waar de taak bij hoort
        $game = $task->game;

        if (!$game) {
            return response()->json(['error' => 'Game not found'], 404);
        }

        // Controleer of de huidige gebruiker de eigenaar van de game is
        if (Auth::id() === $game->user_id) {
            return response()->json(['error' => 'Cannot complete your own task'], 400);
        }

        // Controleer of de taak al is voltooid door de gebruiker
        $followerId = Auth::id();

        $completedTask = FollowerTask::where('task_id', $taskId)
            ->where('follower_id', $followerId)
            ->first();

        $taskExperience = $completedTask->experience;

        $user->experience += $taskExperience;
        $user->save();

        if (!$completedTask) {
            // Als de taak nog niet is voltooid door de huidige volger, retourneer een foutmelding
            return response()->json(['message' => 'Task not yet completed'], 400);
        }

        // Pas de status van de bestaande taak aan naar 'completed'
        $completedTask->status = 'completed';
        $completedTask->save();


        return response()->json(['message' => 'Task marked as complete']);
    }

    public function addTask(Request $request, $gameId)
    {
        $game = Game::findOrFail($gameId);

        // Een nieuwe taak aanmaken voor de maker van het spel
        $task = new Task;
        $task->name = $request->input('name');
        $task->description = $request->input('description');
        $task->experience = $request->input('experience');
        $game->tasks()->save($task); // Opslaan van taak voor de maker

        // Een nieuwe taak aanmaken voor alle volgers van het spel
        $followerTasksData = [];
        $followers = $game->followers;
        foreach ($followers as $follower) {
            $followerTasksData[] = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'experience' => $request->input('experience'),
                'follower_id' => $follower->id,
                'task_id' => $task->id,
                'game_id' => $game->id,
            ];
        }
        FollowerTask::insert($followerTasksData); // Opslaan van taken voor volgers

        return response()->json(['id' => $game->id]);
    }
}

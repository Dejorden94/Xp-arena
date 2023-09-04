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




    public function getUnverifiedTasks()
    {
        // Controleer of de gebruiker een spel heeft gemaakt
        $game = Game::where('user_id', Auth::id())->first();

        if (!$game) {
            // De gebruiker heeft geen spel. Retourneer een succesvolle respons in plaats van een fout.
            return response()->json(['message' => 'No game found for this user'], 200);
        }

        // Haal de taken op die nog niet zijn geverifieerd en niet zijn afgewezen
        $tasks = CompletedTask::where('creator_id', Auth::id())
            ->where('is_verified', false)
            ->where('is_rejected', false)
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

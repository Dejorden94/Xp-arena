<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Task;
use App\Models\User;
use App\Models\CompletedTask;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;


class GameController extends Controller
{


    public function index()
    {
        $games = Game::where('user_id', Auth::id())->get();
        return response()->json($games);
    }

    public function store(Request $request)
    {
        $game = new Game;
        $game->name = $request->input('name');
        $game->user_id = Auth::id();

        // Genereren van een unieke pincode
        $game->pin_code = $this->generateUniquePinCode();

        $game->save();

        return response()->json(['id' => $game->id]);
    }

    private function generateUniquePinCode()
    {
        do {
            $pinCode = mt_rand(1000, 9999);
        } while (Game::where('pin_code', $pinCode)->exists());

        return $pinCode;
    }


    public function followGame(Request $request)
    {
        $pincode = $request->input('pincode');

        $game = Game::where('pin_code', $pincode)->first();

        if (!$game) {
            return response()->json(['error' => 'Invalid pincode'], 404);
        }

        $userId = Auth::id();

        // Check if the user is already following the game
        $isFollower = $game->followers()->where('user_id', $userId)->exists();
        if ($isFollower) {
            return response()->json(['error' => 'User is already a follower of the game'], 400);
        }

        // Add the user as a follower of the game
        $game->followers()->attach($userId);

        // Get all tasks of the game
        $tasks = $game->tasks;

        // Loop through the tasks and add them to the completed_tasks table for the user
        foreach ($tasks as $task) {
            $completedTask = new CompletedTask;
            $completedTask->task_id = $task->id;
            $completedTask->user_id = $userId;
            $completedTask->game_id = $game->id;
            $completedTask->creator_id = $game->user_id;  // Set the creator_id
            $completedTask->is_verified = false; // You can ignore this line if you don't need verification process

            // Fill the other columns from the corresponding task
            $completedTask->name = $task->name;
            $completedTask->description = $task->description;
            $completedTask->experience = $task->experience;
            $completedTask->is_rejected = false; // You might want to set this based on your business logic
            $completedTask->approval_status = 'unverified'; // You might want to set this based on your business logic

            $completedTask->save();
        }

        return response()->json(['message' => 'Game followed successfully']);
    }



    public function followedGames()
    {
        $userId = Auth::id();

        // Haal alle games op die de gebruiker volgt
        $games = Game::whereHas('followers', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return response()->json(['games' => $games]);
    }

    public function show($gameId)
    {
        // Logica om de gamegegevens op te halen op basis van $gameId
        $game = Game::find($gameId);

        if (!$game) {
            return response()->json(['error' => 'Game not found'], 404);
        }

        return response()->json($game);
    }

    public function handleTasks(Request $request, $gameId)
    {
        $game = Game::findOrFail($gameId);

        if ($request->isMethod('get')) {
            // Logica voor GET-verzoek (gegevens ophalen)
            $tasks = $game->tasks;

            return response()->json([
                'message' => 'Tasks retrieved successfully',
                'tasks' => $tasks
            ]);
        } elseif ($request->isMethod('post')) {

            $request->validate([
                'name' => 'required|max:255',
                'description' => 'required',
                'experience' => 'required|integer|min:0', // Zorg ervoor dat 'experience' een positieve geheel getal is
            ]);
            // Logica voor POST-verzoek (gegevens opslaan)
            $task = new Task;
            $task->name = $request->input('name');
            $task->description = $request->input('description');
            $task->experience = $request->input('experience'); // Toevoegen van de 'experience'

            $game->tasks()->save($task);

            return response()->json([
                'message' => 'Task created successfully',
                'task' => $task
            ]);
        } else {
            // Ongeldige HTTP-methode
            return response()->json([
                'message' => 'Invalid HTTP method'
            ], 405); // 405 staat voor Method Not Allowed
        }
    }

    public function deleteTask($gameId, $taskId)
    {
        $game = Game::findOrFail($gameId);

        $task = $game->tasks()->findOrFail($taskId);

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}

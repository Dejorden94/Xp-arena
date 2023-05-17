<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

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
        $game->save();

        return response()->json(['id' => $game->id]);
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
            // Logica voor POST-verzoek (gegevens opslaan)
            $task = new Task;
            $task->name = $request->input('name');
            $task->description = $request->input('description');
    
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

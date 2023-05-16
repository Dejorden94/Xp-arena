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

    public function storeTask(Request $request, $gameId)
{
    $game = Game::findOrFail($gameId);

    $task = new Task;
    $task->name = $request->input('name');
    $task->description = $request->input('description');

    $game->tasks()->save($task);

    return response()->json([
        'message' => 'Task created successfully',
        'task' => $task
    ]);
}
}

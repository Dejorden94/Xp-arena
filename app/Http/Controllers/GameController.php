<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Task;
use App\Models\User;
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

        $userId = Auth::id(); //De waarde van $userIdeis null waarom?

        Log::info('User ID:', ['userId' => $userId]);

        // Controleren of de gebruiker al bestaat
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found',], 404);
        }

        // Controleren of de gebruiker al een volger is van het spel
        $isFollower = $game->followers()->where('user_id', $userId)->exists();
        if ($isFollower) {
            return response()->json(['error' => 'User is already a follower of the game'], 400);
        }

        // Controleren of de gebruiker de maker van het spel is
        if ($game->user_id === $userId) {
            return response()->json(['error' => 'Cannot follow your own game'], 400);
        }

        // Voeg de gebruiker toe als volger van het spel
        $game->followers()->attach($userId);

        return response()->json(['message' => 'Game followed successfully']);
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

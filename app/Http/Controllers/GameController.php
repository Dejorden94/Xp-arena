<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Task;
use App\Models\User;
use App\Models\FollowerTask;
use App\Models\FollowerCriterion;
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
        $request->validate([
            'name' => 'required',
            'game_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $game = new Game;
        $game->name = $request->input('name');
        $game->user_id = Auth::id();
        // Genereren van een unieke pincode
        $game->pin_code = $this->generateUniquePinCode();

        if ($request->hasFile('game_image')) {
            $imageName = time() . '.' . $request->game_image->extension();
            $request->game_image->move(public_path('images/game-images'), $imageName);
            $game->image = '/images/game-images/' . $imageName;
        }

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

    public function uploadGameImage(Request $request)
    {
        $request->validate([
            'game_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageName = time() . '.' . $request->game_image->extension();
        $request->game_image->move(public_path('images/game-images'), $imageName);

        return back()
            ->with('succes', 'You have successfully uploaded your image.')
            ->with('image', $imageName);
    }

    public function followGame(Request $request)
    {
        $pincode = $request->input('pincode');

        $game = Game::where('pin_code', $pincode)->first();

        if (!$game) {
            return response()->json(['error' => 'Invalid pincode'], 404);
        }

        $userId = Auth::id();

        // Controleren of de gebruiker al bestaat
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
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

        // Voeg de taken van het spel toe aan de follower_tasks tabel
        $tasks = $game->tasks;

        foreach ($tasks as $task) {
            // Maak een nieuwe FollowerTask
            $followerTask = FollowerTask::create([
                'name' => $task->name,
                'description' => $task->description,
                'experience' => $task->experience,
                'follower_id' => $userId,
                'task_id' => $task->id,
                'image' => $task->image,
                'game_id' => $game->id,
                'checkpoint_id' => $task->checkpoint_id,
            ]);

            // Kopieer de criteria van de originele taak naar de nieuwe FollowerTask
            foreach ($task->criteria as $criterion) {
                FollowerCriterion::create([
                    'description' => $criterion->description,
                    'is_met' => $criterion->is_met,
                    'follower_task_id' => $followerTask->id,
                    'difficulty' => $criterion->difficulty
                ]);
            }
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

    public function getFollowedGameTasks($userId, $gameId)
    {
        $game = Game::findOrFail($gameId);

        // Controleer of de gebruiker de game volgt
        $isFollower = $game->followers()->where('user_id', $userId)->exists();

        if (!$isFollower) {
            return response()->json(['error' => 'User is not a follower of the game'], 403);
        }

        // Haal de taken op uit de follower_tasks tabel
        $tasks = FollowerTask::where('game_id', $gameId)
            ->where('follower_id', $userId)
            ->get();

        return response()->json(['tasks' => $tasks]);
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

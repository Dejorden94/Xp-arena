<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function index()
{
    $games = Game::all();
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
}

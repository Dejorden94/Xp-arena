<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checkpoint;

class CheckpointController extends Controller
{
    public function store(Request $request)
    {
        // Valideer de request data
        $request->validate([
            'title' => 'required|string|max:255',
            'game_id' => 'required|integer|exists:games,id',
            // Voeg hier eventuele andere validatieregels toe
        ]);

        // Creëer een nieuw checkpoint
        $checkpoint = new Checkpoint;
        $checkpoint->name = $request->title;
        $checkpoint->game_id = $request->game_id;
        // Stel hier eventuele andere eigenschappen in
        $checkpoint->save();

        // Stuur een response terug
        return response()->json($checkpoint);
    }
}

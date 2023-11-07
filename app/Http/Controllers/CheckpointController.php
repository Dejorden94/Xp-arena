<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checkpoint;
use App\Models\Task;

class CheckpointController extends Controller
{
    public function index($gameId)
    {
        // Verifieer dat de game bestaat en de gebruiker toegang heeft tot deze game
        // ...

        // Haal alle checkpoints op voor de gegeven game
        $checkpoints = Checkpoint::where('game_id', $gameId)->with('tasks')->get();

        return response()->json($checkpoints);
    }

    public function store(Request $request)
    {
        // Valideer de binnenkomende request
        $validatedData = $request->validate([
            'game_id' => 'required|exists:games,id',
            'name' => 'required|string|max:255',
            // Voeg hier eventuele andere velden toe die u nodig heeft
        ]);

        // Maak een nieuw checkpoint
        $checkpoint = new Checkpoint();
        $checkpoint->game_id = $validatedData['game_id'];
        $checkpoint->name = $validatedData['name'];
        // Stel hier eventuele andere eigenschappen in
        $checkpoint->save();

        return response()->json([
            'message' => 'Checkpoint successfully created',
            'checkpoint' => $checkpoint
        ]);
    }

    public function addTaskToCheckpoint(Request $request, $checkpointId)
    {
        $request->validate([
            'task_id' => 'required|integer|exists:tasks,id',
        ]);

        $task = Task::find($request->task_id);
        $task->checkpoint_id = $checkpointId;
        $task->save();

        return response()->json(['message' => 'Task successfully added to checkpoint']);
    }

    public function assignTask(Request $request, $checkpointId)
    {
        // Valideer de binnenkomende request
        $validatedData = $request->validate([
            'task_id' => 'required|exists:tasks,id', // Zorg ervoor dat de taak bestaat
        ]);

        // Vind het checkpoint en verifieer dat het bestaat
        $checkpoint = Checkpoint::findOrFail($checkpointId);

        // Vind de taak en verifieer dat het bestaat
        $task = Task::findOrFail($validatedData['task_id']);

        // Wijs de taak toe aan het checkpoint
        $task->checkpoint_id = $checkpoint->id;
        $task->save();

        return response()->json([
            'message' => 'Task successfully assigned to checkpoint',
            'task' => $task
        ]);
    }
}

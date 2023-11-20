<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Game;
use App\Models\User;
use App\Models\FollowerTask;
use App\Models\TaskCriterion;
use App\Models\FollowerCriterion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;



class TaskController extends Controller
{
    public function completeTask($taskId, Request $request)
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
        $tasks = FollowerTask::where('game_id', $game->id)
            ->where('status', 'completed')
            ->get();


        return response()->json($tasks);
    }

    public function deleteFollowerTask($taskId)
    {
        try {
            // Zoek de FollowerTask op basis van het ID
            $followerTask = FollowerTask::findOrFail($taskId);

            // Voer eventuele extra logica uit voordat je de FollowerTask verwijdert

            // Verwijder de FollowerTask
            $followerTask->delete();

            return response()->json(['message' => 'FollowerTask succesvol verwijderd'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Fout bij het verwijderen van de FollowerTask'], 500);
        }
    }

    public function updateStatus($id)
    {
        // Zoek de follower_task
        $followerTask = FollowerTask::find($id);

        if (!$followerTask) {
            return response()->json(['message' => 'Follower task not found'], 404);
        }

        // Haal de follower_id op uit de follower_task
        $followerId = $followerTask->follower_id;

        // Werk de status bij naar 'rejected'
        $followerTask->status = 'rejected';
        $followerTask->save();

        // Hier kun je eventueel de ervaringspunten van de volger aanpassen.
        $follower = User::find($followerId);



        if ($follower) {
            Log::info('Value of $follower:', ['follower' => $follower]);
            // Trek de experience points van de taak af van de follower user experience points
            $follower->experience -= $followerTask->experience;
            $follower->save();

            return response()->json(['message' => 'Follower task status updated to rejected'], 200);
        } else {
            return response()->json(['message' => 'Follower not found'], 404);
        }
    }

    public function addTask(Request $request, $gameId)
    {
        $game = Game::findOrFail($gameId);

        // Ophalen van de eerste checkpoint van de game
        $firstCheckpoint = $game->checkpoints()->orderBy('id')->first();

        // Controleer of er een checkpoint bestaat
        if (!$firstCheckpoint) {
            return response()->json(['error' => 'Geen checkpoints gevonden voor deze game'], 404);
        }

        // Een nieuwe taak aanmaken voor de maker van het spel
        $task = new Task;
        $task->name = $request->input('name');
        $task->description = $request->input('description');
        $task->experience = $request->input('experience');
        $task->checkpoint_id = $firstCheckpoint->id;
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
                'checkpoint_id' => $task->checkpoint_id,
            ];
        }
        FollowerTask::insert($followerTasksData); // Opslaan van taken voor volgers

        return response()->json(['id' => $game->id]);
    }

    // Criteria functions 
    public function addCriteria(Request $request, $taskId)
    {
        // Vind de taak en controleer of deze bestaat
        $task = Task::with('criteria')->findOrFail($taskId);

        // Maak het nieuwe criterium voor de taak
        $criterion = new TaskCriterion([
            'description' => $request->input('description'),
            'is_met' => false
        ]);

        // Sla het criterium op gekoppeld aan de taak
        $task->criteria()->save($criterion);

        $followerTask = $this->findFollowerTask($task);

        // Controleer of de 'follower_task' bestaat
        if ($followerTask) {
            $followerCriterion = new FollowerCriterion([
                'follower_task_id' => $followerTask->id,
                'description' => $request->input('description'),
                'is_met' => false
            ]);

            // Sla het nieuwe criterium op in de 'follower_criteria' tabel
            $followerCriterion->save();
        }

        // Redirect terug met een succesbericht
        return redirect()->back()->with('success', 'Criterium toegevoegd aan zowel taak als volger!');
    }

    /**
     * Een voorbeeldmethode om de overeenkomstige 'follower_task' te vinden.
     * Je moet deze methode implementeren op basis van je applicatielogica.
     */
    protected function findFollowerTask(Task $task)
    {
        // Implementeer de logica om de overeenkomstige 'follower_task' te vinden
        // Dit kan een relatie zijn of een andere manier om de gekoppelde 'follower_task' te identificeren
        // Voorbeeld:
        // return FollowerTask::where('original_task_id', $task->id)->first();

        return FollowerTask::where('task_id', $task->id)->first();
    }

    public function checkFollowerCriteria($followerTaskId)
    {
        $followerTask = FollowerTask::with('criteria')->findOrFail($followerTaskId);
        return response()->json($followerTask->criteria);
    }

    public function editCriteria(Request $request, $criterionId)
    {
        $criterion = TaskCriterion::findOrFail($criterionId);
        $criterion->description = $request->input('description');
        $criterion->save();

        return redirect()->back()->with('success', 'Criterium bijgewerkt!');
    }
    public function deleteCriteria($criterionId)
    {
        $criterion = TaskCriterion::findOrFail($criterionId);
        $criterion->delete();

        return redirect()->back()->with('success', 'Criterium verwijderd!');
    }
    public function checkCriteria($taskId)
    {
        // Haal de taak op met alle bijbehorende criteria
        $task = Task::with('criteria')->findOrFail($taskId);

        // Geef alle criteria terug als JSON
        return response()->json($task->criteria);
    }

    public function toggleCriterionMet($taskId, $criterionId)
    {
        // Vind het criterium in de database
        $criterion = TaskCriterion::where('id', $criterionId)->where('task_id', $taskId)->first();


        if (!$criterion) {
            return response()->json(['error' => 'Criterium niet gevonden'], 404);
        }

        // Toggle de is_met waarde
        $criterion->is_met = !$criterion->is_met;
        $criterion->save();

        return response()->json(['success' => true, 'is_met' => $criterion->is_met]);
    }

    public function toggleFollowerCriterionMet($taskId, $criterionId)
    {
        // Vind het criterium in de database
        $criterion = FollowerCriterion::where('id', $criterionId)->where('follower_task_id', $taskId)->first();

        if (!$criterion) {
            return response()->json(['error' => 'Criterium niet gevonden'], 404);
        }

        // Toggle de is_met waarde
        $criterion->is_met = !$criterion->is_met;
        $criterion->save();

        return response()->json(['success' => true, 'is_met' => $criterion->is_met]);
    }

    public function updateTask(Request $request, $taskId)
    {
        // Zoek de taak op basis van het gegeven ID
        $task = Task::find($taskId);

        // Als de taak niet wordt gevonden, retourneer een foutmelding
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Update de velden van de taak met de waarden uit het verzoek
        $task->name = $request->input('name', $task->name); // Als er geen nieuwe naam wordt gegeven, behoud dan de oude naam
        $task->description = $request->input('description', $task->description); // Als er geen nieuwe beschrijving wordt gegeven, behoud dan de oude beschrijving
        $task->experience = $request->input('experience', $task->experience); // Als er geen nieuwe ervaring wordt gegeven, behoud dan de oude ervaring
        $task->checkpoint_id = $request->input('checkpoint_id', $task->checkpoint_id);

        // Sla de wijzigingen op
        $task->save();

        // Update de bijbehorende criteria als ze zijn meegegeven in het verzoek
        if ($request->has('criteria')) {
            foreach ($request->input('criteria') as $updatedCriterion) {
                $criterion = $task->criteria->where('id', $updatedCriterion['id'])->first();

                if ($criterion) {
                    $criterion->description = $updatedCriterion['description'];
                    $criterion->is_met = $updatedCriterion['is_met'];
                    $criterion->save();
                }
            }
        }

        // Retourneer een succesbericht
        return response()->json(['message' => 'Task and associated criteria updated successfully']);
    }

    public function getTaskDescription($taskId)
    {
        $task = Task::find($taskId);
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        return response()->json(['description' => $task->description]);
    }
}

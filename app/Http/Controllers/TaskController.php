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
    protected function calculateLevelFromXP($xp)
    {
        $level = 0;
        while ($xp >= pow($level, 3) * 1000) {
            $level++;
        }
        return $level - 1; // Aftrekken van 1 omdat het level met 1 verhoogt wordt na de laatste voldoening aan de voorwaarde
    }


    public function completeTask($taskId, Request $request)
    {
        //Zoek de huidige user
        $user = Auth::user();


        // Zoek de taak
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['error' => 'Quest not found'], 404);
        }

        // Zoek de game waar de taak bij hoort
        $game = $task->game;

        if (!$game) {
            return response()->json(['error' => 'Game not found'], 404);
        }

        // Controleer of de huidige gebruiker de eigenaar van de game is
        if (Auth::id() === $game->user_id) {
            return response()->json(['error' => 'Cannot complete your own quest'], 400);
        }

        // Controleer of de taak al is voltooid door de gebruiker
        $followerId = Auth::id();

        $completedTask = FollowerTask::where('task_id', $taskId)
            ->where('follower_id', $followerId)
            ->first();

        if (!$completedTask) {
            // Als de taak nog niet is voltooid door de huidige volger, retourneer een foutmelding
            return response()->json(['message' => 'Task not yet completed'], 400);
        }

        // Pas de status van de bestaande taak aan naar 'completed'
        $completedTask->status = 'reviewing';
        $completedTask->save();

        if ($user->level < 100) {
            $taskExperience = $completedTask->experience;

            $user->experience += $taskExperience;
            // Bereken het nieuwe level en update het in de database
            $newLevel = $this->calculateLevelFromXP($user->experience);
            $user->level = $newLevel;
            $user->save();
        }


        return response()->json(['message' => 'Task marked as complete']);
    }

    public function updateTaskExperience($taskId, Request $request)
    {
        $task = FollowerTask::find($taskId);
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $newExperience = $request->input('experience');
        $experienceDifference = $newExperience - $task->experience;

        $follower = User::find($task->follower_id);
        if ($follower && $follower->level < 100) {
            $follower->experience = max(0, $follower->experience + $experienceDifference);
            $newLevel = $this->calculateLevelFromXP($follower->experience);
            $follower->level = $newLevel;
            $follower->save();

            // Update de ervaringspunten van de taak om te voorkomen dat ze opnieuw worden toegevoegd
            $task->experience = $newExperience;
            $task->save();

            return response()->json(['message' => 'Follower experience updated successfully']);
        } else {
            return response()->json(['error' => 'Follower not found or already at max level'], 404);
        }
    }

    public function updateUserLevel($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Bereken het nieuwe level op basis van de huidige XP van de gebruiker
        $newLevel = $this->calculateLevelFromXP($user->experience);

        // Update het level van de gebruiker als het is veranderd
        if ($user->level != $newLevel) {
            $user->level = $newLevel;
            $user->save();

            return response()->json(['message' => 'User level updated successfully', 'newLevel' => $newLevel]);
        }

        return response()->json(['message' => 'No level change required']);
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
            ->where('status', 'reviewing')
            ->get();


        return response()->json($tasks);
    }

    public function acceptFollowerTask($taskId)
    {
        try {
            // Zoek de FollowerTask op basis van het ID

            $followerTask = FollowerTask::findOrFail($taskId);

            // zet status naar completed van de  FollowerTask
            $followerTask->status = 'completed';
            $followerTask->save();

            return response()->json(['message' => 'Quest goedgekeurd'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Fout bij het goedkeuren van de Quest'], 500);
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

        // Haal de gebruiker op
        $follower = User::find($followerId);

        if ($follower) {
            // Trek de experience points van de taak af van de follower user experience points
            // Alleen als de gebruiker niet level 100 is
            if ($follower->level < 100) {
                $follower->experience -= $followerTask->experience;
                $follower->save();
            }

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

        $totalExperiencePoints = 0;
        if (isset($validatedData['criteria']) && is_array($validatedData['criteria'])) {
            $totalExperiencePoints = array_sum(array_map(function ($criterion) {
                return $criterion['experience_points'] ?? 0;
            }, $validatedData['criteria']));
        }

        $task->name = $request->input('name');
        $task->description = $request->input('description');
        $task->experience = $totalExperiencePoints;
        $task->checkpoint_id = $firstCheckpoint->id;
        $game->tasks()->save($task); // Opslaan van taak voor de maker

        // Een nieuwe taak aanmaken voor alle volgers van het spel
        $followerTasksData = [];
        $followers = $game->followers;
        foreach ($followers as $follower) {
            $followerTasksData[] = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'experience' => 0,
                'follower_id' => $follower->id,
                'task_id' => $task->id,
                'game_id' => $game->id,
                'checkpoint_id' => $task->checkpoint_id,
            ];
        }
        FollowerTask::insert($followerTasksData); // Opslaan van taken voor volgers

        return response()->json(['id' => $game->id]);
    }

    public function getFollowerTaskCriteria($followerTaskId)
    {
        // Zoek de FollowerTask op basis van het ID
        $followerTask = FollowerTask::with('criteria')->find($followerTaskId);

        // Controleer of de FollowerTask bestaat
        if (!$followerTask) {
            return response()->json(['error' => 'FollowerTask not found'], 404);
        }

        // Haal de criteria op die geassocieerd zijn met de FollowerTask
        $criteria = $followerTask->criteria;

        // Retourneer de criteria als JSON
        return response()->json($criteria);
    }

    // Criteria functions 
    public function addCriteria(Request $request, $taskId)
    {
        // Punten voor moeilijkheidsgraden.
        $difficultyExperienceMap = [
            'makkelijk' => 10,
            'normaal' => 20,
            'moeilijk' => 30
        ];

        // Valideer de request
        $validatedData = $request->validate([
            'description' => 'required|string|max:255',
            'difficulty' => 'required|in:makkelijk,normaal,moeilijk'
        ]);

        // Vind de taak en controleer of deze bestaat
        $task = Task::with('criteria')->findOrFail($taskId);

        // Bepaal de ervaringspunten gebaseerd op de moeilijkheidsgraad
        $experiencePoints = $difficultyExperienceMap[$validatedData['difficulty']] ?? 0;

        // Maak het nieuwe criterium voor de taak
        $criterion = new TaskCriterion([
            'description' => $validatedData['description'],
            'is_met' => false,
            'difficulty' => $validatedData['difficulty'],
            'experience_points' => $experiencePoints
        ]);

        // Sla het criterium op gekoppeld aan de taak
        $task->criteria()->save($criterion);

        // Update de totale ervaring van de taak
        $task->experience += $experiencePoints;
        $task->save();

        $followerTask = $this->findFollowerTask($task);

        // Controleer of de 'follower_task' bestaat
        if ($followerTask) {
            $followerCriterion = new FollowerCriterion([
                'follower_task_id' => $followerTask->id,
                'description' => $validatedData['description'],
                'is_met' => false,
                'difficulty' => $validatedData['difficulty'],
                'experience_points' => $experiencePoints
            ]);

            // Sla het nieuwe criterium op in de 'follower_criteria' tabel
            $followerCriterion->save();
        }

        // Redirect terug met een succesbericht
        return redirect()->back()->with('success', 'Criterium toegevoegd aan zowel taak als volger!');
    }

    protected function findFollowerTask(Task $task)
    {
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
        $criterion->difficulty = $request->input('difficulty');
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
        $task = Task::find($taskId);
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $difficultyExperienceMap = [
            'makkelijk' => 10,
            'normaal' => 20,
            'moeilijk' => 30
        ];

        if ($request->has('criteria')) {
            foreach ($request->input('criteria') as $updatedCriterion) {
                $criterion = TaskCriterion::where('id', $updatedCriterion['id'])->where('task_id', $taskId)->first();

                if ($criterion) {
                    $criterion->description = $updatedCriterion['description'];
                    $criterion->is_met = $updatedCriterion['is_met'];

                    if (isset($updatedCriterion['difficulty'])) {
                        $criterion->difficulty = $updatedCriterion['difficulty'];
                        $criterion->experience_points = $difficultyExperienceMap[$updatedCriterion['difficulty']] ?? 0;
                    }
                    $criterion->save();
                }
            }
        }

        // Update de totale ervaringspunten van de taak
        $totalExperiencePoints = $task->criteria->sum('experience_points');

        $task->name = $request->input('name', $task->name);
        $task->description = $request->input('description', $task->description);
        $task->experience = $totalExperiencePoints;
        $task->checkpoint_id = $request->input('checkpoint_id', $task->checkpoint_id);
        $task->save();

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

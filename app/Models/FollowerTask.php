<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowerTask extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'game_id',
        'experience',
        'game_id',
        'follower_id',
        'task_id',
        'checkpoint_id'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function follower()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function criteria()
    {
        return $this->hasMany(FollowerCriterion::class);
    }
    public function hasCompletedAllTasks($checkpointId)
    {
        $tasks = Task::where('checkpoint_id', $checkpointId)->pluck('id');
        $completedTasks = $this->tasks()->whereIn('task_id', $tasks)->count();
        return $completedTasks === $tasks->count();
    }
}

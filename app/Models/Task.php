<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TaskCriterion;


class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'game_id',
        'experience',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($task) {
            // Verwijder de bijbehorende taken uit de follower_tasks tabel
            $followerTasks = FollowerTask::where('task_id', $task->id)->get();
            foreach ($followerTasks as $followerTask) {
                $followerTask->delete();
            }
        });
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function completedTasks()
    {
        return $this->hasMany(CompletedTask::class);
    }

    public function followerTasks()
    {
        return $this->hasMany(FollowerTask::class, 'task_id');
    }

    public function criteria()
    {
        return $this->hasMany(TaskCriterion::class);
    }
}

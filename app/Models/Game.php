<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'game_id', 'user_id');
    }

    public function followerTasks()
    {
        return $this->hasMany(FollowerTask::class, 'game_id');
    }

    public function completedTasks()
    {
        return $this->hasMany(CompletedTask::class);
    }
}

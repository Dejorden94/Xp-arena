<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'game_id',
        'experience',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function completedTasks()
    {
        return $this->hasMany(CompletedTask::class);
    }
}

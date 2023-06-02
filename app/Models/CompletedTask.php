<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletedTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'game_id',
        'completion_status',
        'is_rejected',
        'creator_id'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}

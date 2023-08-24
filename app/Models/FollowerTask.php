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
        'task_id'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function follower()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

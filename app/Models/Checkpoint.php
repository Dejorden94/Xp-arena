<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'game_id', 'order'];
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}

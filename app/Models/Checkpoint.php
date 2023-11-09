<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'game_id', 'order'];
    public function index($gameId)
    {
        $checkpoints = Checkpoint::where('game_id', $gameId)->with('tasks')->get();

        return response()->json($checkpoints);
    }
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('order');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}

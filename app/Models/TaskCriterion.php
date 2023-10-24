<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCriterion extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'description', 'is_met'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}

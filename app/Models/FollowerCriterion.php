<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowerCriterion extends Model
{
    use HasFactory;
    protected $fillable = ['description', 'is_met', 'follower_task_id', 'difficulty'];

    public function followerTask()
    {
        return $this->belongsTo(FollowerTask::class);
    }
}

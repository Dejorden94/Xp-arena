<?php

namespace App\Observers;

use App\Models\TaskCriterion;
use App\Models\FollowerCriterion;

class CriterionObserver
{
    /**
     * Handle the Criterion "created" event.
     */
    public function created(TaskCriterion $criterion): void
    {
        //
    }

    /**
     * Handle the Criterion "updated" event.
     */
    public function updated(TaskCriterion $criterion): void
    {
        // Update de FollowerCriteria wanneer de originele Criterion wordt bijgewerkt
        FollowerCriterion::where('criterion_id', $criterion->id)->update([
            'description' => $criterion->description,
            'is_met' => $criterion->is_met,
        ]);
    }

    /**
     * Handle the Criterion "deleted" event.
     */
    public function deleted(TaskCriterion $criterion): void
    {
        //
    }

    /**
     * Handle the Criterion "restored" event.
     */
    public function restored(TaskCriterion $criterion): void
    {
        //
    }

    /**
     * Handle the Criterion "force deleted" event.
     */
    public function forceDeleted(TaskCriterion $criterion): void
    {
        //
    }
}

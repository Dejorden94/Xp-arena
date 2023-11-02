<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\TaskCriterion;
use App\Models\Task;

use App\Observers\TaskObserver;
use App\Observers\CriterionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        TaskCriterion::observe(CriterionObserver::class);
    }
}

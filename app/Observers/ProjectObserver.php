<?php

namespace App\Observers;

use App\Models\project;
use Illuminate\Support\Facades\Auth;

class ProjectObserver
{
    public function creating(project $project)
    {
        $project->created_by = Auth::id();
        $project->updated_at = null;
    }

    public function updating(project $project)
    {
        $project->updated_by = Auth::id();
    }
    /**
     * Handle the project "created" event.
     */
    public function created(project $project): void
    {
        //
    }

    /**
     * Handle the project "updated" event.
     */
    public function updated(project $project): void
    {
        //
    }

    /**
     * Handle the project "deleted" event.
     */
    public function deleted(project $project): void
    {
        //
    }

    /**
     * Handle the project "restored" event.
     */
    public function restored(project $project): void
    {
        //
    }

    /**
     * Handle the project "force deleted" event.
     */
    public function forceDeleted(project $project): void
    {
        //
    }
}

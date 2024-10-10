<?php

namespace App\Observers;

use App\Models\PlanningAssignment;
use Illuminate\Support\Facades\Auth;

class PlanningAssignmentObserver
{
    public function creating(PlanningAssignment $planningAssignment)
    {
        $planningAssignment->created_by = Auth::id();
        $planningAssignment->updated_at = null;
    }

    public function updating(PlanningAssignment $planningAssignment)
    {
        $planningAssignment->updated_by = Auth::id();
    }
    /**
     * Handle the PlanningAssignment "created" event.
     */
    public function created(PlanningAssignment $planningAssignment): void
    {
        //
    }

    /**
     * Handle the PlanningAssignment "updated" event.
     */
    public function updated(PlanningAssignment $planningAssignment): void
    {
        //
    }

    /**
     * Handle the PlanningAssignment "deleted" event.
     */
    public function deleted(PlanningAssignment $planningAssignment): void
    {
        //
    }

    /**
     * Handle the PlanningAssignment "restored" event.
     */
    public function restored(PlanningAssignment $planningAssignment): void
    {
        //
    }

    /**
     * Handle the PlanningAssignment "force deleted" event.
     */
    public function forceDeleted(PlanningAssignment $planningAssignment): void
    {
        //
    }
}

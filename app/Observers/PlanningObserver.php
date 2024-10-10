<?php

namespace App\Observers;

use App\Models\Planning;
use Illuminate\Support\Facades\Auth;

class PlanningObserver
{
    public function creating(Planning $planning)
    {
        $planning->created_by = Auth::id();
        $planning->updated_at = null;
    }

    public function updating(Planning $planning)
    {
        $planning->updated_by = Auth::id();
    }
    /**
     * Handle the Planning "created" event.
     */
    public function created(Planning $planning): void
    {
        //
    }

    /**
     * Handle the Planning "updated" event.
     */
    public function updated(Planning $planning): void
    {
        //
    }

    /**
     * Handle the Planning "deleted" event.
     */
    public function deleted(Planning $planning): void
    {
        //
    }

    /**
     * Handle the Planning "restored" event.
     */
    public function restored(Planning $planning): void
    {
        //
    }

    /**
     * Handle the Planning "force deleted" event.
     */
    public function forceDeleted(Planning $planning): void
    {
        //
    }
}

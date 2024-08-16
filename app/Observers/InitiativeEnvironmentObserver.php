<?php

namespace App\Observers;

use App\Models\InitiativeEnvironment;
use Illuminate\Support\Facades\Auth;

class InitiativeEnvironmentObserver
{
    public function creating(InitiativeEnvironment $initiativeEnvironment)
    {
        $initiativeEnvironment->created_by = Auth::id();
        $initiativeEnvironment->updated_at = null;
    }

    public function updating(InitiativeEnvironment $initiativeEnvironment)
    {
        $initiativeEnvironment->updated_by = Auth::id();
    }
    /**
     * Handle the InitiativeEnvironment "created" event.
     */
    public function created(InitiativeEnvironment $initiativeEnvironment): void
    {
        //
    }

    /**
     * Handle the InitiativeEnvironment "updated" event.
     */
    public function updated(InitiativeEnvironment $initiativeEnvironment): void
    {
        //
    }

    /**
     * Handle the InitiativeEnvironment "deleted" event.
     */
    public function deleted(InitiativeEnvironment $initiativeEnvironment): void
    {
        //
    }

    /**
     * Handle the InitiativeEnvironment "restored" event.
     */
    public function restored(InitiativeEnvironment $initiativeEnvironment): void
    {
        //
    }

    /**
     * Handle the InitiativeEnvironment "force deleted" event.
     */
    public function forceDeleted(InitiativeEnvironment $initiativeEnvironment): void
    {
        //
    }
}

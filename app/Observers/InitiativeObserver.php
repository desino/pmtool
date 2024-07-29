<?php

namespace App\Observers;

use App\Models\Initiative;
use Illuminate\Support\Facades\Auth;

class InitiativeObserver
{
    public function creating(Initiative $initiative)
    {
        $initiative->created_by = Auth::id();
        // $initiative->updated_by = Auth::id();
        $initiative->updated_at = null;
    }
    /**
     * Handle the Initiative "created" event.
     */
    public function created(Initiative $initiative): void
    {
        //
    }

    /**
     * Handle the Initiative "updated" event.
     */
    public function updated(Initiative $initiative): void
    {
        //
    }

    /**
     * Handle the Initiative "deleted" event.
     */
    public function deleted(Initiative $initiative): void
    {
        //
    }

    /**
     * Handle the Initiative "restored" event.
     */
    public function restored(Initiative $initiative): void
    {
        //
    }

    /**
     * Handle the Initiative "force deleted" event.
     */
    public function forceDeleted(Initiative $initiative): void
    {
        //
    }
}

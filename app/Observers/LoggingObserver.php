<?php

namespace App\Observers;

use App\Models\Logging;
use Illuminate\Support\Facades\Auth;

class LoggingObserver
{
    public function creating(Logging $logging)
    {
        $logging->created_by = Auth::id();
        $logging->updated_at = null;
    }

    public function updating(Logging $logging)
    {
        $logging->updated_by = Auth::id();
    }
    /**
     * Handle the Logging "created" event.
     */
    public function created(Logging $logging): void
    {
        //
    }

    /**
     * Handle the Logging "updated" event.
     */
    public function updated(Logging $logging): void
    {
        //
    }

    /**
     * Handle the Logging "deleted" event.
     */
    public function deleted(Logging $logging): void
    {
        //
    }

    /**
     * Handle the Logging "restored" event.
     */
    public function restored(Logging $logging): void
    {
        //
    }

    /**
     * Handle the Logging "force deleted" event.
     */
    public function forceDeleted(Logging $logging): void
    {
        //
    }
}

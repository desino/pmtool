<?php

namespace App\Observers;

use App\Models\ReleaseTicket;
use Illuminate\Support\Facades\Auth;

class ReleaseTicketObserver
{
    public function creating(ReleaseTicket $releaseTicket)
    {
        $releaseTicket->created_by = Auth::id();
        $releaseTicket->updated_at = null;
    }

    public function updating(ReleaseTicket $releaseTicket)
    {
        $releaseTicket->updated_by = Auth::id();
    }
    /**
     * Handle the ReleaseTicket "created" event.
     */
    public function created(ReleaseTicket $releaseTicket): void
    {
        //
    }

    /**
     * Handle the ReleaseTicket "updated" event.
     */
    public function updated(ReleaseTicket $releaseTicket): void
    {
        //
    }

    /**
     * Handle the ReleaseTicket "deleted" event.
     */
    public function deleted(ReleaseTicket $releaseTicket): void
    {
        //
    }

    /**
     * Handle the ReleaseTicket "restored" event.
     */
    public function restored(ReleaseTicket $releaseTicket): void
    {
        //
    }

    /**
     * Handle the ReleaseTicket "force deleted" event.
     */
    public function forceDeleted(ReleaseTicket $releaseTicket): void
    {
        //
    }
}

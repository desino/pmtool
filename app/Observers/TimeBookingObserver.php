<?php

namespace App\Observers;

use App\Models\TimeBooking;
use Illuminate\Support\Facades\Auth;

class TimeBookingObserver
{
    public function creating(TimeBooking $timeBooking)
    {
        $timeBooking->created_by = Auth::id();
        $timeBooking->updated_at = null;
    }

    public function updating(TimeBooking $timeBooking)
    {
        $timeBooking->updated_by = Auth::id();
    }
    /**
     * Handle the TimeBooking "created" event.
     */
    public function created(TimeBooking $timeBooking): void
    {
        //
    }

    /**
     * Handle the TimeBooking "updated" event.
     */
    public function updated(TimeBooking $timeBooking): void
    {
        //
    }

    /**
     * Handle the TimeBooking "deleted" event.
     */
    public function deleted(TimeBooking $timeBooking): void
    {
        //
    }

    /**
     * Handle the TimeBooking "restored" event.
     */
    public function restored(TimeBooking $timeBooking): void
    {
        //
    }

    /**
     * Handle the TimeBooking "force deleted" event.
     */
    public function forceDeleted(TimeBooking $timeBooking): void
    {
        //
    }
}

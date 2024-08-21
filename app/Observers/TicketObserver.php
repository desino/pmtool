<?php

namespace App\Observers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketObserver
{
    public function creating(Ticket $ticket)
    {
        $ticket->created_by = Auth::id();
        $ticket->updated_at = null;
    }

    public function updating(Ticket $ticket)
    {
        $ticket->updated_by = Auth::id();
    }
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
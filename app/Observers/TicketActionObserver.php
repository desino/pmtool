<?php

namespace App\Observers;

use App\Models\TicketAction;
use Illuminate\Support\Facades\Auth;

class TicketActionObserver
{
    public function creating(TicketAction $ticketAction)
    {
        $ticketAction->created_by = Auth::id();
        $ticketAction->updated_at = null;
    }

    public function updating(TicketAction $ticketAction)
    {
        $ticketAction->updated_by = Auth::id();
    }
    /**
     * Handle the TicketAction "created" event.
     */
    public function created(TicketAction $ticketAction): void
    {
        //
    }

    /**
     * Handle the TicketAction "updated" event.
     */
    public function updated(TicketAction $ticketAction): void
    {
        //
    }

    /**
     * Handle the TicketAction "deleted" event.
     */
    public function deleted(TicketAction $ticketAction): void
    {
        //
    }

    /**
     * Handle the TicketAction "restored" event.
     */
    public function restored(TicketAction $ticketAction): void
    {
        //
    }

    /**
     * Handle the TicketAction "force deleted" event.
     */
    public function forceDeleted(TicketAction $ticketAction): void
    {
        //
    }
}

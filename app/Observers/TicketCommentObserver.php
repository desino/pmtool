<?php

namespace App\Observers;

use App\Models\TicketComment;
use Illuminate\Support\Facades\Auth;

class TicketCommentObserver
{
    public function creating(TicketComment $ticketComment)
    {
        $ticketComment->created_by = Auth::id();
        $ticketComment->updated_at = null;
    }

    public function updating(TicketComment $ticketComment)
    {
        $ticketComment->updated_by = Auth::id();
    }
    /**
     * Handle the TicketComment "created" event.
     */
    public function created(TicketComment $ticketComment): void
    {
        //
    }

    /**
     * Handle the TicketComment "updated" event.
     */
    public function updated(TicketComment $ticketComment): void
    {
        //
    }

    /**
     * Handle the TicketComment "deleted" event.
     */
    public function deleted(TicketComment $ticketComment): void
    {
        //
    }

    /**
     * Handle the TicketComment "restored" event.
     */
    public function restored(TicketComment $ticketComment): void
    {
        //
    }

    /**
     * Handle the TicketComment "force deleted" event.
     */
    public function forceDeleted(TicketComment $ticketComment): void
    {
        //
    }
}

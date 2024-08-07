<?php

namespace App\Observers;

use App\Models\Functionality;
use Illuminate\Support\Facades\Auth;

class FunctionalityObserver
{
    public function creating(Functionality $functionality)
    {
        $functionality->created_by = Auth::id();
        $functionality->order_no = $functionality->max('order_no')+1;
        // $client->updated_by = Auth::id();
        $functionality->updated_at = null;
    }

    public function updating(Functionality $functionality)
    {
        $functionality->updated_by = Auth::id();
    }
    /**
     * Handle the Functionality "created" event.
     */
    public function created(Functionality $functionality): void
    {
        //
    }

    /**
     * Handle the Functionality "updated" event.
     */
    public function updated(Functionality $functionality): void
    {
        //
    }

    /**
     * Handle the Functionality "deleted" event.
     */
    public function deleted(Functionality $functionality): void
    {
        //
    }

    /**
     * Handle the Functionality "restored" event.
     */
    public function restored(Functionality $functionality): void
    {
        //
    }

    /**
     * Handle the Functionality "force deleted" event.
     */
    public function forceDeleted(Functionality $functionality): void
    {
        //
    }
}
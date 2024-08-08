<?php

namespace App\Observers;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientObserver
{
    public function creating(Client $client)
    {
        $client->created_by = Auth::id();
        $client->updated_at = null;
    }

    public function updating(Client $client)
    {
        $client->updated_by = Auth::id();
    }
    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {
        //
    }
}

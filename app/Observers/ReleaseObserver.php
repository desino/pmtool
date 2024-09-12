<?php

namespace App\Observers;

use App\Models\Release;
use Illuminate\Support\Facades\Auth;

class ReleaseObserver
{

    public function creating(Release $release)
    {
        $release->status = Release::UNPROCESSED_RELEASE;
        $release->created_by = Auth::id();
        $release->updated_at = null;
    }

    public function updating(Release $release)
    {
        $release->updated_by = Auth::id();
    }
    /**
     * Handle the Release "created" event.
     */
    public function created(Release $release): void
    {
        //
    }

    /**
     * Handle the Release "updated" event.
     */
    public function updated(Release $release): void
    {
        //
    }

    /**
     * Handle the Release "deleted" event.
     */
    public function deleted(Release $release): void
    {
        //
    }

    /**
     * Handle the Release "restored" event.
     */
    public function restored(Release $release): void
    {
        //
    }

    /**
     * Handle the Release "force deleted" event.
     */
    public function forceDeleted(Release $release): void
    {
        //
    }
}

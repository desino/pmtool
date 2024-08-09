<?php

namespace App\Observers;

use App\Models\Section;
use Illuminate\Support\Facades\Auth;

class SectionObserver
{
    public function creating(Section $section)
    {
        $section->created_by = Auth::id();
        $section->order_no = $section->max('order_no')+1;
        $section->updated_at = null;
    }

    public function updating(Section $section)
    {
        $section->updated_by = Auth::id();
    }
    /**
     * Handle the Section "created" event.
     */
    public function created(Section $section): void
    {
        //
    }

    /**
     * Handle the Section "updated" event.
     */
    public function updated(Section $section): void
    {
        //
    }

    /**
     * Handle the Section "deleted" event.
     */
    public function deleted(Section $section): void
    {
        Section::where('order_no','>=',$section->order_no)
        ->decrement('order_no');
    }

    /**
     * Handle the Section "restored" event.
     */
    public function restored(Section $section): void
    {
        //
    }

    /**
     * Handle the Section "force deleted" event.
     */
    public function forceDeleted(Section $section): void
    {
        //
    }
}
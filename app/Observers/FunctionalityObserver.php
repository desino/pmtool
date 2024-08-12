<?php

namespace App\Observers;

use App\Models\Functionality;
use Illuminate\Support\Facades\Auth;

class FunctionalityObserver
{
    public function creating(Functionality $functionality)
    {
        $newFunctionalityorderNo = $functionality->where('section_id',$functionality->section_id)->max('order_no')+1;
        $sectionOrderNo = $functionality->section->order_no;
        $functionality->display_name = $sectionOrderNo.".".$newFunctionalityorderNo." ".$functionality->name;
        $functionality->created_by = Auth::id();
        $functionality->order_no = $newFunctionalityorderNo;
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

    }

    /**
     * Handle the Functionality "deleted" event.
     */
    public function deleted(Functionality $functionality): void
    {
        Functionality::where('section_id',$functionality->section_id)
        ->where('order_no','>=',$functionality->order_no)
        ->each( function ($eachMoveToSectionfunctionality, $index) {
            $eachMoveToSectionfunctionality->decrement('order_no');
            $eachMoveToSectionfunctionality->display_name = $eachMoveToSectionfunctionality->section->order_no.".".$eachMoveToSectionfunctionality->order_no." ".$eachMoveToSectionfunctionality->name;
            $eachMoveToSectionfunctionality->save();
        });
        // ->decrement('order_no');
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
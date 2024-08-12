<?php

namespace App\Observers;

use App\Models\Functionality;
use Illuminate\Support\Facades\Auth;

class FunctionalityObserver
{
    public function creating(Functionality $functionality)
    {
        $functionality->created_by = Auth::id();
        $functionality->order_no = $functionality->where('section_id',$functionality->section_id)->max('order_no')+1;
        $functionality->updated_at = null;
    }

    public function updating(Functionality $functionality)
    {
        $oldFuntionality = $functionality->getOriginal();
        // $postData = request()->post();
        // // if($oldFuntionality['section_id'] != $functionality->section_id && !isset($postData['move_to_section_id'])){
        // if($oldFuntionality['section_id'] != $functionality->section_id){
        //     $functionality->order_no = $functionality->where('section_id',$functionality->section_id)->max('order_no')+1;
        // }
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
        // $oldFuntionality = $functionality->getOriginal();
        // if($oldFuntionality['section_id'] != $functionality->section_id){
        //     Functionality::where('section_id',$oldFuntionality['section_id'])
        //     ->where('order_no','>=',$oldFuntionality['order_no'])
        //     ->decrement('order_no');
        // }
    }

    /**
     * Handle the Functionality "deleted" event.
     */
    public function deleted(Functionality $functionality): void
    {
        Functionality::where('section_id',$functionality->section_id)
        ->where('order_no','>=',$functionality->order_no)
        ->decrement('order_no');
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
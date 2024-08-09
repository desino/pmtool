<?php

namespace App\Services;

use App\Models\Functionality;
use App\Models\Initiative;
use App\Models\Section;
use Faker\Provider\ar_EG\Person;

Class SolutionDesignServicec
{
    public static function getSectionsWithFunctionalities($request) {
        $sectionWithFunctionalities = Section::with('functionalities')
        ->InitiativeId($request->post('initiative_id'))
        ->orderBy('order_no')
        ->get();
        return $sectionWithFunctionalities;
    }

    public static function storeFunctionality($request, $data) {
        $functionality = Functionality::create($data);
        return $functionality;
    }

    public static function updateFunctionality($request, $data) {
        $functionality = Functionality::find($data['functionality_id']);
        $data['id'] = $functionality->id;
        $functionality->update($data);
        return $functionality;
    }

    public static function deleteFunctionality($request) {
        $functionality = Functionality::find($request->post('functionality_id'));
        $functionality->delete();
        return $functionality;
    }

    public static function deleteSection($request) {
        $section = Section::find($request->post('section_id'));
        $section->functionalities()->delete();
        $section->delete();
        return $section;
    }

    public static function updateSection($request) {
        $postData = $request->post();
        $postData['name'] = $postData['section_name'];
        $section = Section::find($request->post('section_id'));
        $section->update($postData);
        return $section;
    }

    public static function updateFunctionalityOrderNo($request) {
        $postData = $request->post();

        $postData = $request->post();
        $sectionId = $postData['section_id'];
        $itemId = $postData['id'];
        $newOrderNo = $postData['order_no'];

        $functionalities = Functionality::where('section_id', $sectionId)
        ->orderBy('order_no')
        ->get();

        $itemToMove = $functionalities->find($itemId);

        if (!$itemToMove) {
            return;
        }

        $currentOrderNo = $itemToMove->order_no;

        if ($currentOrderNo === $newOrderNo) {
            return;
        }

        foreach ($functionalities as $functionality) {
            if ($functionality->id !== $itemToMove->id) {
                if ($currentOrderNo < $newOrderNo) {
                    // If moving down, decrease the order number for items between current and new positions
                    if ($functionality->order_no > $currentOrderNo && $functionality->order_no <= $newOrderNo) {
                        $functionality->decrement('order_no');
                    }
                } else {
                    // If moving up, increase the order number for items between new and current positions
                    if ($functionality->order_no < $currentOrderNo && $functionality->order_no >= $newOrderNo) {
                        $functionality->increment('order_no');
                    }
                }
            }
        }

        // Update the order number of the item to move
        $itemToMove->order_no = $newOrderNo;
        $itemToMove->save();
    }

    public static function updateSectionOrderNo($request){
        $postData = $request->post();

        $postData = $request->post();
        $itemId = $postData['id'];
        $newOrderNo = $postData['order_no'];

        $sectiones = Section::orderBy('order_no')->get();

        $itemToMove = $sectiones->find($itemId);

        if (!$itemToMove) {
            return;
        }

        $currentOrderNo = $itemToMove->order_no;

        if ($currentOrderNo === $newOrderNo) {
            return;
        }

        foreach ($sectiones as $sectione) {
            if ($sectione->id !== $itemToMove->id) {
                if ($currentOrderNo < $newOrderNo) {
                    // If moving down, decrease the order number for items between current and new positions
                    if ($sectione->order_no > $currentOrderNo && $sectione->order_no <= $newOrderNo) {
                        $sectione->decrement('order_no');
                    }
                } else {
                    // If moving up, increase the order number for items between new and current positions
                    if ($sectione->order_no < $currentOrderNo && $sectione->order_no >= $newOrderNo) {
                        $sectione->increment('order_no');
                    }
                }
            }
        }

        // Update the order number of the item to move
        $itemToMove->order_no = $newOrderNo;
        $itemToMove->save();
    }
}
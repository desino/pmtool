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
        $oldFuntionality = clone $functionality;
        $data['id'] = $functionality->id;
        if($oldFuntionality['section_id'] != $request->post('section_id')){
            $orderNo = Functionality::where('section_id',$request->post('section_id'))->max('order_no')+1;
            $request->merge(['section_id' => $oldFuntionality['section_id']]);
            $request->merge(['id' => $functionality->id]);
            $request->merge(['move_to_section_id' => $request->post('section_id')]);
            $request->merge(['order_no' => $orderNo]);
            self::updateFunctionalityOrderNo($request);
        }
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
        $postData = $request->all();

        $retData = [
            'status' => true,
            'message' => null,
            'functionality' => null
        ];

        $sectionId = $postData['section_id'];
        $itemId = $postData['id'];
        $moveToSectionPosition = $postData['order_no'];
        $moveToSectionId = $postData['move_to_section_id'] ?? null;


        if($moveToSectionId){
            //refresh ordering of current section except moved functionality
            Functionality::where('section_id', $sectionId)
            ->orderBy('order_no')
            ->whereNot('id', $itemId)
            ->each( function ($eachMoveToSectionfunctionality, $index) {
                $eachMoveToSectionfunctionality->order_no = $index+1;
                $eachMoveToSectionfunctionality->save();
            });

            //refresh ordering of move to section
            Functionality::where('section_id', $moveToSectionId)
            ->orderBy('order_no')
            ->each( function ($eachMoveToSectionfunctionality, $index) {
                $eachMoveToSectionfunctionality->order_no = $index+1;
                $eachMoveToSectionfunctionality->save();
            });

            //New section: change orders of functionalities comes after this being move functionality based on position from request
            Functionality::where('section_id', $moveToSectionId)
            ->orderBy('order_no')
            ->where('order_no', '>=', $moveToSectionPosition)
            ->each( function ($eachMoveToSectionfunctionality, $index) {
                $eachMoveToSectionfunctionality->increment('order_no');
            });

            Functionality::where('id', $itemId)
            ->update([
                'order_no' => $moveToSectionPosition,
                'section_id' => $moveToSectionId,
            ]);
        } else {
            //refresh ordering of current section except moved functionality
            Functionality::where('section_id', $sectionId)
            ->orderBy('order_no')
            ->whereNot('id', $itemId)
            ->each( function ($eachMoveToSectionfunctionality, $index) {
                $eachMoveToSectionfunctionality->order_no = $index+1;
                $eachMoveToSectionfunctionality->save();
            });


            //New section: change orders of functionalities comes after this being move functionality based on position from request
            Functionality::where('section_id', $sectionId)
            ->orderBy('order_no')
            ->where('order_no', '>=', $moveToSectionPosition)
            ->each( function ($eachMoveToSectionfunctionality, $index) {
                $eachMoveToSectionfunctionality->increment('order_no');
            });

            Functionality::where('id', $itemId)
            ->update([
                'order_no' => $moveToSectionPosition,
            ]);
        }
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
        return true;
    }
}
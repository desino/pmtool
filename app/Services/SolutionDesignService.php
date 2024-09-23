<?php

namespace App\Services;

use App\Models\Functionality;
use App\Models\Initiative;
use App\Models\Section;
use Faker\Provider\ar_EG\Person;
use Illuminate\Contracts\Database\Eloquent\Builder;

class SolutionDesignService
{
    public static function getSectionsWithFunctionalities($request)
    {
        $sectionWithFunctionalities = Section::with(['functionalities' => function ($query) use ($request) {
            $query->when($request->post('name') != '', function (Builder $query) use ($request) {
                $query->whereLike('display_name', '%' . $request->post('name') . '%');
            });
        }])
            ->InitiativeId($request->post('initiative_id'))
            ->orderBy('order_no')
            ->get();
        return $sectionWithFunctionalities;
    }

    public static function getSection($id, $initiativeId = null)
    {
        $section = Section::when($initiativeId != null, function ($q) use ($initiativeId) {
            $q->where('initiative_id', $initiativeId);
        })
            ->find($id);
        return $section;
    }

    public static function storeFunctionality($request, $data)
    {
        $functionality = Functionality::create($data);
        return $functionality;
    }

    public static function updateFunctionality($request, $data)
    {
        $functionality = Functionality::find($data['functionality_id']);
        $oldFuntionality = clone $functionality;
        $data['id'] = $functionality->id;
        $data['display_name'] = $functionality->section->order_no . "." . $functionality->order_no . " " . $request->post('name');
        if ($oldFuntionality['section_id'] != $request->post('section_id')) {
            $orderNo = Functionality::where('section_id', $request->post('section_id'))->max('order_no') + 1;
            $request->merge(['section_id' => $oldFuntionality['section_id']]);
            $request->merge(['id' => $functionality->id]);
            $request->merge(['move_to_section_id' => $request->post('section_id')]);
            $request->merge(['order_no' => $orderNo]);
            self::updateFunctionalityOrderNo($request);
        }
        $functionality->update($data);
        return $functionality;
    }

    public static function deleteSection($request)
    {
        $section = Section::find($request->post('section_id'));
        $section->functionalities()->delete();
        $section->delete();
        return $section;
    }

    public static function updateSection($request, $section)
    {
        $postData = $request->post();
        $postData['name'] = $postData['section_name'];
        $postData['display_name'] = $section->order_no . " " . $postData['name'];
        $section->update($postData);
        return $section;
    }


    public static function updateFunctionalityOrderNo($request)
    {
        $postData = $request->all();

        $sectionId = $postData['section_id'];
        $itemId = $postData['id'];
        $moveToSectionPosition = $postData['order_no'];
        $moveToSectionId = $postData['move_to_section_id'] ?? null;


        if ($moveToSectionId) {
            //refresh ordering of current section except moved functionality
            Functionality::with('section')->where('section_id', $sectionId)
                ->orderBy('order_no')
                ->whereNot('id', $itemId)
                ->each(function ($eachMoveToSectionfunctionality, $index) {
                    $newOrderNo = $index + 1;
                    $eachMoveToSectionfunctionality->order_no = $newOrderNo;
                    $eachMoveToSectionfunctionality->display_name = $eachMoveToSectionfunctionality->section->order_no . "." . $newOrderNo . " " . $eachMoveToSectionfunctionality->name;
                    $eachMoveToSectionfunctionality->save();
                });

            //refresh ordering of move to section
            Functionality::with('section')->where('section_id', $moveToSectionId)
                ->orderBy('order_no')
                ->each(function ($eachMoveToSectionfunctionality, $index) {
                    $newOrderNo = $index + 1;
                    $eachMoveToSectionfunctionality->order_no = $newOrderNo;
                    $eachMoveToSectionfunctionality->display_name = $eachMoveToSectionfunctionality->section->order_no . "." . $newOrderNo . " " . $eachMoveToSectionfunctionality->name;
                    $eachMoveToSectionfunctionality->save();
                });

            //New section: change orders of functionalities comes after this being move functionality based on position from request
            Functionality::where('section_id', $moveToSectionId)
                ->orderBy('order_no')
                ->where('order_no', '>=', $moveToSectionPosition)
                ->each(function ($eachMoveToSectionfunctionality, $index) {
                    $eachMoveToSectionfunctionality->increment('order_no');
                    $eachMoveToSectionfunctionality->display_name = $eachMoveToSectionfunctionality->section->order_no . "." . $eachMoveToSectionfunctionality->order_no . " " . $eachMoveToSectionfunctionality->name;
                    $eachMoveToSectionfunctionality->save();
                });

            $functionality = Functionality::with('section')->where('id', $itemId)->first();
            $section = Section::find($moveToSectionId);
            $functionality->update([
                'order_no' => $moveToSectionPosition,
                'section_id' => $moveToSectionId,
                'display_name' => $section->order_no . "." . $moveToSectionPosition . " " . $functionality->name,
            ]);
        } else {
            //refresh ordering of current section except moved functionality
            Functionality::where('section_id', $sectionId)
                ->orderBy('order_no')
                ->whereNot('id', $itemId)
                ->each(function ($eachMoveToSectionfunctionality, $index) {
                    $newOrderNo = $index + 1;
                    $eachMoveToSectionfunctionality->order_no = $newOrderNo;
                    $eachMoveToSectionfunctionality->display_name = $eachMoveToSectionfunctionality->section->order_no . "." . $newOrderNo . " " . $eachMoveToSectionfunctionality->name;
                    $eachMoveToSectionfunctionality->save();
                });


            //New section: change orders of functionalities comes after this being move functionality based on position from request
            Functionality::where('section_id', $sectionId)
                ->orderBy('order_no')
                ->where('order_no', '>=', $moveToSectionPosition)
                ->each(function ($eachMoveToSectionfunctionality, $index) {
                    $eachMoveToSectionfunctionality->increment('order_no');
                    $eachMoveToSectionfunctionality->display_name = $eachMoveToSectionfunctionality->section->order_no . "." . $eachMoveToSectionfunctionality->order_no . " " . $eachMoveToSectionfunctionality->name;
                    $eachMoveToSectionfunctionality->save();
                });


            $functionality = Functionality::with('section')->where('id', $itemId)->first();
            $sectionOrderNo = $functionality->section->order_no;
            $functionality->update([
                'order_no' => $moveToSectionPosition,
                'display_name' => $sectionOrderNo . "." . $moveToSectionPosition . " " . $functionality->name,
            ]);
        }
        return true;
    }

    public static function updateSectionOrderNo($request)
    {
        $postData = $request->post();

        $itemId = $postData['id'];
        $moveToSectionPosition = $postData['order_no'];

        //refresh ordering of current section except moved functionality
        Section::orderBy('order_no')
            ->where('initiative_id', $postData['initiative_id'])
            ->whereNot('id', $itemId)
            ->each(function ($eachMoveToSection, $index) {
                $newOrderNo = $index + 1;
                $eachMoveToSection->order_no = $newOrderNo;
                $eachMoveToSection->display_name = $newOrderNo . " " . $eachMoveToSection->name;
                $eachMoveToSection->save();
                $eachMoveToSection->functionalities->each(function ($functionality, $index) use ($eachMoveToSection) {
                    $functionality->display_name = $eachMoveToSection->order_no . "." . $functionality->order_no . " " . $functionality->name;
                    $functionality->save();
                });
            });


        //New section: change orders of functionalities comes after this being move functionality based on position from request
        Section::orderBy('order_no')
            ->where('initiative_id', $postData['initiative_id'])
            ->where('order_no', '>=', $moveToSectionPosition)
            ->each(function ($eachMoveToSection, $index) {
                $eachMoveToSection->increment('order_no');
                $eachMoveToSection->display_name = $eachMoveToSection->order_no . " " . $eachMoveToSection->name;
                $eachMoveToSection->save();
            });


        $section = Section::with('functionalities')
            ->where('initiative_id', $postData['initiative_id'])
            ->where('id', $itemId)
            ->first();
        $section->update([
            'order_no' => $moveToSectionPosition,
            'display_name' => $moveToSectionPosition . " " . $section->name,
        ]);
        $section->functionalities->each(function ($functionality, $index) use ($section) {
            $functionality->display_name = $section->order_no . "." . $functionality->order_no . " " . $functionality->name;
            $functionality->save();
        });
        return true;
    }
}

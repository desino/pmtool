<?php

namespace App\Services;

use App\Models\Functionality;
use App\Models\Initiative;
use App\Models\Section;

Class SolutionDesignServicec
{
    public static function getSectionsWithFunctionalities($request) {
        $sectionWithFunctionalities = Section::with('functionalities')
        ->InitiativeId($request->post('initiative_id'))
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
}
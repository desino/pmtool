<?php

namespace App\Services;

use App\Models\Initiative;
use App\Models\Section;

Class SolutionDesignServicec
{
    public static function getSectionsWithFunctionalities($request) {
        $sectionWithFunctionalities = Section::InitiativeId($request->post('initiative_id'))->get();
        return $sectionWithFunctionalities;
    }
}
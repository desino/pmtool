<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Services\InitiativeService;
use App\Services\SolutionDesignServicec;
use Illuminate\Http\Request;

class SolutionDesignController extends Controller
{
    public function index(Request $request){
        $getSectionsWithFunctionalities = SolutionDesignServicec::getSectionsWithFunctionalities($request);
        return ApiHelper::response(true, '', $getSectionsWithFunctionalities, 200);
    }
    public function getInitiative(Request $request){
        $initiative = InitiativeService::getInitiative($request);
        if(!$initiative){
            return ApiHelper::response(false, __('messages.solution_design.initial_data_not_found'), '', 200);
        }
        return ApiHelper::response(true, '', $initiative, 200);
    }

    public function storeSection(Request $request){
        $status = false;
        $section = collect([]);
        try {
            $postData = $request->post();
            $postData['name'] = $request->post('name') ?? __('meesage.solution_design.section.create_untitled_text');
            $section = Section::create($postData);
            $status = true;
            $meesage = __('messages.solution_design.sectino.store_success');
            $statusCode = 200;
        } catch (\Exception $e) {
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
        }
        return ApiHelper::response($status, $meesage, $section, $statusCode);
    }
}
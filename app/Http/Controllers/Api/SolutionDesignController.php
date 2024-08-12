<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FunctionalityRequest;
use App\Http\Requests\Api\SectionRequest;
use App\Models\Functionality;
use App\Models\Section;
use App\Services\InitiativeService;
use App\Services\SolutionDesignServicec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $section->functionalities;
            $status = true;
            $meesage = __('messages.solution_design.sectino.store_success');
            $statusCode = 200;
        } catch (\Exception $e) {
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
        }
        return ApiHelper::response($status, $meesage, $section, $statusCode);
    }

    public function storeUpdateFunctionality(FunctionalityRequest $request){
        $validatData = $request->validated();

        $status = false;
        $retData = [
            'functionality'=> ""
        ];
        DB::beginTransaction();
        try {
            $status = true;
            $transactionType = "";
            if(isset($validatData['functionality_id'])){
                $functionality = SolutionDesignServicec::updateFunctionality($request,$validatData);
                $meesage = __('messages.solution_design.functionality.update_success');
                $transactionType = "updated";
            } else {
                $functionality = SolutionDesignServicec::storeFunctionality($request,$validatData);
                $meesage = __('messages.solution_design.functionality.store_success');
                $transactionType = "created";
            }
            $statusCode = 200;
            $retData = [
                'functionality'=> $functionality,
                'transactionType' => $transactionType
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
        }
        return ApiHelper::response($status, $meesage, $retData, $statusCode);
    }

    public function deleteFunctionality(Request $request){
        DB::beginTransaction();
        $status = false;
        try {
            DB::commit();
            SolutionDesignServicec::deleteFunctionality($request);
            $statusCode = 200;
            $meesage = __('messages.solution_design.functionality.delete_success');
        } catch (\Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
        }
        return ApiHelper::response($status, $meesage, '', $statusCode);
    }
    public function deleteSection(Request $request){
        DB::beginTransaction();
        $status = false;
        try {
            SolutionDesignServicec::deleteSection($request);
            $statusCode = 200;
            $meesage = __('messages.solution_design.section.delete_success');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
        }
        return ApiHelper::response($status, $meesage, '', $statusCode);
    }

    public function updateSection(SectionRequest $request){
        DB::beginTransaction();
        $status = false;
        $section = collect([]);
        try {
            $section = SolutionDesignServicec::updateSection($request);
            $statusCode = 200;
            $meesage = __('messages.solution_design.section.update_success');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
        }
        return ApiHelper::response($status, $meesage, $section, $statusCode);
    }

    public function updateFunctionalityOrderNo(Request $request){
        $statusCode = 200;
        $status = false;
        $meesage = __('messages.solution_design.section.update_functionality_order_no_success');

        DB::beginTransaction();
        try {
            SolutionDesignServicec::updateFunctionalityOrderNo($request);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            return ApiHelper::response($status, $meesage, collect([]), $statusCode);
        }

        $updatedFunctionality = Functionality::find($request->post('id'));
        return ApiHelper::response($status, $meesage, $updatedFunctionality, $statusCode);
    }

    public function updateSectionOrderNo(Request $request){
        DB::beginTransaction();
        $status = false;
        try {
            $retData = SolutionDesignServicec::updateSectionOrderNo($request);
            $statusCode = 200;
            $meesage = __('messages.solution_design.section.update_section_order_no_success');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
        }
        return ApiHelper::response($status, $meesage, '', $statusCode);
    }
}
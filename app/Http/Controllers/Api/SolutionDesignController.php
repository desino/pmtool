<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FunctionalityRequest;
use App\Http\Requests\Api\SectionRequest;
use App\Models\Functionality;
use App\Models\Section;
use App\Services\ClientService;
use App\Services\InitiativeService;
use App\Services\SolutionDesignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolutionDesignController extends Controller
{
    public function index(Request $request)
    {
        $getSectionsWithFunctionalities = SolutionDesignService::getSectionsWithFunctionalities($request);
        return ApiHelper::response(true, '', $getSectionsWithFunctionalities, 200);
    }
    public function download(Request $request)
    {
        $getSectionsWithFunctionalities = SolutionDesignService::getSectionsWithFunctionalitiesForDownload($request);
        return ApiHelper::response(true, '', $getSectionsWithFunctionalities, 200);
    }
    public function getInitiative(Request $request)
    {
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response(false, __('messages.solution_design.initial_data_not_found'), '', 200);
        }
        return ApiHelper::response(true, '', $initiative, 200);
    }

    public function storeSection(SectionRequest $request)
    {
        $status = false;
        $section = collect([]);

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        try {
            $postData = $request->post();
            $postData['name'] = $request->post('section_name');
            $section = Section::create($postData);
            $section->functionalities;
            $status = true;
            $message = __('messages.solution_design.section.store_success');
            $statusCode = 200;
        } catch (\Exception $e) {
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, $section, $statusCode);
    }

    public function updateSection(SectionRequest $request)
    {
        DB::beginTransaction();
        $status = false;
        $section = collect([]);

        $section = SolutionDesignService::getSection($request->post('section_id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.section.store.section_not_exist'), '', 400);
        }

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        try {
            $section = SolutionDesignService::updateSection($request, $section);
            $statusCode = 200;
            $message = __('messages.solution_design.section.update_success');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, $section, $statusCode);
    }

    public function storeUpdateFunctionality(FunctionalityRequest $request)
    {
        $validateData = $request->validated();
        $status = false;
        $retData = [
            'functionality' => ""
        ];

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.sectino.client_not_exist'), '', 400);
        }

        $section = SolutionDesignService::getSection($request->post('section_id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.section_not_exist'), '', 400);
        }

        if ($request->post('functionality_id')) {
            $functionality = Functionality::find($request->post('functionality_id'));
            if (!$functionality) {
                return ApiHelper::response($status, __('messages.solution_design.functionality.functionality_not_exist'), '', 400);
            }
        }

        DB::beginTransaction();
        try {
            $status = true;
            $transactionType = "";
            if (isset($validateData['functionality_id'])) {
                $functionality = SolutionDesignService::updateFunctionality($request, $validateData);
                $message = __('messages.solution_design.functionality.update_success');
                $transactionType = "updated";
            } else {
                $functionality = SolutionDesignService::storeFunctionality($request, $validateData);
                $message = __('messages.solution_design.functionality.store_success');
                $transactionType = "created";
            }
            $statusCode = 200;
            $retData = [
                'functionality' => $functionality,
                'transactionType' => $transactionType
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, $retData, $statusCode);
    }

    public function deleteFunctionality(Request $request)
    {
        $status = false;

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        $section = SolutionDesignService::getSection($request->post('section_id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.section_not_exist'), '', 400);
        }

        $functionality = Functionality::where('section_id', $request->post('section_id'))->find($request->post('id'));
        if (!$functionality) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.functionality_not_exist'), '', 400);
        }

        DB::beginTransaction();
        try {
            $functionality->delete();
            DB::commit();
            $statusCode = 200;
            $message = __('messages.solution_design.functionality.delete_success');
        } catch (\Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }
    public function deleteSection(Request $request)
    {
        $status = false;

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        $section = SolutionDesignService::getSection($request->post('id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.section_not_exist'), '', 400);
        }

        DB::beginTransaction();
        try {
            $section->functionalities()->delete();
            $section->delete();
            // SolutionDesignService::deleteSection($request);
            $statusCode = 200;
            $message = __('messages.solution_design.section.delete_success');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }

    public function updateFunctionalityOrderNo(Request $request)
    {
        $statusCode = 200;
        $status = false;
        $message = __('messages.solution_design.section.update_functionality_order_no_success');

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        $section = SolutionDesignService::getSection($request->post('section_id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.section_not_exist'), '', 400);
        }

        $functionality = Functionality::where('section_id', $request->post('section_id'))->find($request->post('id'));
        if (!$functionality) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.functionality_not_exist'), '', 400);
        }

        DB::beginTransaction();
        try {
            SolutionDesignService::updateFunctionalityOrderNo($request);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
            return ApiHelper::response($status, $message, collect([]), $statusCode);
        }

        $updatedFunctionality = Functionality::find($request->post('id'));
        return ApiHelper::response($status, $message, $updatedFunctionality, $statusCode);
    }

    public function updateSectionOrderNo(Request $request)
    {
        DB::beginTransaction();
        $status = false;

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        $section = SolutionDesignService::getSection($request->post('id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.section_not_exist'), '', 400);
        }
        try {
            $retData = SolutionDesignService::updateSectionOrderNo($request);
            $statusCode = 200;
            $message = __('messages.solution_design.section.update_section_order_no_success');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }
}

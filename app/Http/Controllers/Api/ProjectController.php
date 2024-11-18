<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProjectRequest;
use App\Models\Project;
use App\Services\InitiativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.initiative.dont_have_permission'), null, 404);
        }
        // $perPage = $request->input('per_page', 30);

        $filters = $request->post('filters');
        $projects = Project::select(
            'id',
            'initiative_id',
            'name',
            'status',
        )
            ->withCount('tickets')
            ->when($request->post('initiative_id') != '', function ($q) use ($request) {
                $q->where('initiative_id', $request->post('initiative_id'));
            })
            ->when(
                isset($filters['active']) && $filters['active'] !== true && isset($filters['inactive']) && $filters['inactive'] !== true,
                function ($q) {
                    $q->where('status', '=', 2);
                }
            )
            ->when(
                isset($filters['active']) && $filters['active'] === true && isset($filters['inactive']) && $filters['inactive'] === true,
                function ($q) {
                    $q->where(function ($query) {
                        $query->where('status', 1)
                            ->orWhere('status', 0);
                    });
                }
            )
            ->when(
                isset($filters['active']) && $filters['active'] === true && (!isset($filters['inactive']) || $filters['inactive'] !== true),
                function ($q) {
                    $q->where('status', 1);
                }
            )
            ->when(
                isset($filters['inactive']) && $filters['inactive'] === true && (!isset($filters['active']) || $filters['active'] !== true),
                function ($q) {
                    $q->where('status', 0);
                }
            )
            ->orderBy('id', 'desc')
            ->paginate(30);
        $parsedProjects = ApiHelper::parsePagination($projects);
        $responseData = [
            'projects' => $parsedProjects,
        ];
        return ApiHelper::response(true, __('messages.project.get_list_success'), $responseData, 200);
    }

    public function changeStatus(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.initiative.dont_have_permission'), null, 404);
        }
        $status = false;
        $requestData = $request->all();

        $project = Project::withCount('tickets')->where('initiative_id', $requestData['initiative_id'])->find($requestData['id']);
        if (!$project) {
            return ApiHelper::response($status, __('messages.project.not_found'), '', 400);
        }

        if ($project->tickets_count > 0 && $requestData['status'] == 0) {
            return ApiHelper::response($status, __('messages.solution_design.section.tickets_exist'), '', 400);
        }

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $status = true;
        $message = __('messages.project.update_status_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $updateData = [
                'status' => $requestData['status'] ? 1 : 0,
            ];
            $project->update($updateData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }

    public function update(ProjectRequest $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.initiative.dont_have_permission'), null, 404);
        }
        $status = false;
        $requestData = $request->all();
        $project = Project::where('initiative_id', $requestData['initiative_id'])->find($requestData['id']);
        if (!$project) {
            return ApiHelper::response($status, __('messages.project.not_found'), '', 400);
        }
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $status = true;
        $message = __('messages.project.update_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $project->update($requestData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }
}

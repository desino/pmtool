<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Functionality;
use App\Models\Release;
use App\Services\InitiativeService;
use Illuminate\Http\Request;

class DeploymentController extends Controller
{

    public function index(Request $request, $initiative_id)
    {
        $status = false;
        $initiative = InitiativeService::getInitiative($request, $initiative_id);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        $filters = $request->filters;
        $release = Release::select(
            'id',
            'initiative_id',
            'name',
            'is_major',
            'version',
            'status',
            'tags',
            'processed_at',
            'created_at',
        )
            ->withCount('tickets')
            ->where('initiative_id', $initiative_id)
            ->when($filters['name'] != '', function ($query) use ($filters) {
                $query->whereLike('name', '%' . $filters['name'] . '%');
            })
            ->when($filters['ticket_name'] != '', function ($query) use ($filters) {
                $query->whereHas('tickets.ticket', function ($query) use ($filters) {
                    $query->whereLike('composed_name', '%' . $filters['ticket_name'] . '%');
                });
            })
            ->when(!empty($filters['functionalities']), function ($query) use ($filters) {
                $query->whereHas('tickets.ticket', function ($query) use ($filters) {
                    $query->whereHas('functionality', function ($query) use ($filters) {
                        $query->whereIn('id', array_column($filters['functionalities'], 'id'));
                    });
                });
            })
            ->paginate(10);
        return ApiHelper::response(true, '', $release, 200);
    }

    public function getInitiativeDataForDeployments(Request $request, $initiative_id)
    {
        $status = false;
        $initiative = InitiativeService::getInitiative($request, $initiative_id);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $functionalities = Functionality::whereHas('section', function ($query) use ($initiative_id) {
            $query->where('initiative_id', $initiative_id);
        })->get(['id', 'display_name']);
        $data = array(
            'functionalities' => $functionalities,
        );
        return ApiHelper::response(true, '', $data, 200);
    }
}

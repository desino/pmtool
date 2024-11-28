<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Models\Logging;
use App\Models\User;
use App\Services\InitiativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogsController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.initiative_overview.dont_have_permission'), null, 404);
        }
        $filters = $request->get('filters');

        $logging = Logging::select('*')
            ->with([
                'ticket' => function ($query) {
                    $query->select('id', 'name', 'initiative_id');
                },
                'createdBy'
            ])
            ->when(!empty($filters['initiative_id']), function ($query) use ($filters) {
                $query->whereHas('ticket.initiative', function ($query) use ($filters) {
                    $query->where('initiatives.id', $filters['initiative_id']['id']);
                });
            })
            ->when(!empty($filters['activity_type']), function ($query) use ($filters) {
                // $query->where('activity_type', $filters['activity_type']['id']);
                $query->whereIn('activity_type', array_column($filters['activity_type'], 'id'));
            })
            ->when(!empty($filters['activity_detail']), function ($query) use ($filters) {
                $query->whereIn('activity_detail', array_column($filters['activity_detail'], 'id'));
            })
            ->when(!empty($filters['user_id']), function ($query) use ($filters) {
                $query->where('created_by', $filters['user_id']['id']);
            })
            ->orderBy('id', 'desc')
            ->paginate(30);
        return ApiHelper::response(true, '', $logging, 200);
    }

    public function getInitiativeDataForActivityLogs(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.initiative_overview.dont_have_permission'), null, 404);
        }

        // $initiatives = InitiativeService::getInitiativesForHeaderSelectBox($request);
        $initiatives = Initiative::get();
        $allActivityTypes = Logging::getAllActivityTypes();
        $allActivityDetails = Logging::getAllActivityDetails();
        $users = User::get();
        $retData = [
            'initiatives' => $initiatives,
            'allActivityTypes' => $allActivityTypes,
            'allActivityDetails' => $allActivityDetails,
            'users' => $users,
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }
}

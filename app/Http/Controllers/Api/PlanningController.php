<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Planning;
use App\Services\InitiativeService;
use App\Services\PlanningService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlanningController extends Controller
{
    public function index(Request $request)
    {
        $requestData = $request->all();
        $currentWeek = Carbon::now()->startOfWeek();
        $startWeek = "";
        $endWeek = "";
        $startWeekDate = "";
        $endWeekDate = "";
        if ($request->get('previous_or_next_of_week') == '-1') {
            $startWeek = Carbon::parse($request->get('start_date'))->subWeek(4)->startOfWeek();
            $endWeek = Carbon::parse($request->get('end_date'))->startOfWeek();
        } else if ($request->get('previous_or_next_of_week') == '1') {
            $startWeek = Carbon::parse($request->get('start_date'))->startOfWeek();
            $endWeek = Carbon::parse($request->get('end_date'))->addWeek(4)->startOfWeek();
        } else if ($request->get('previous_or_next_of_week') == '0') {
            $startWeek = Carbon::now()->subWeek(4)->startOfWeek();
            $endWeek = Carbon::now()->addWeek(8)->startOfWeek();
        }
        $startWeekDate = $startWeek->toDateString();
        $endWeekDate = $endWeek->toDateString();
        // echo "start: " . $startWeekDate . " end: " . $endWeekDate;
        // exit;

        $loadWeeks = [];
        while ($startWeek->lte($endWeek)) {
            $loadWeeks[] = [
                'date' => $startWeek->toDateString(),
                'week' => $startWeek->weekOfYear,
                'display_week_name' => 'Week ' . $startWeek->format('d/m'),
                'is_current_week' => $startWeek->toDateString() == $currentWeek->toDateString(),
            ];
            $startWeek->addWeek();
        }

        $plannings = Planning::select(
            'plannings.id',
            'plannings.planning_assignment_id',
            'plannings.planning_date',
            'plannings.hours',
            'planning_assignments.initiative_id',
            'initiatives.name as initiative_name',
            'planning_assignments.user_id',
            'users.name as user_name',
            DB::raw('WEEK(plannings.planning_date) as week')
        )
            ->join('planning_assignments', 'planning_assignments.id', 'plannings.planning_assignment_id')
            ->join('initiatives', 'initiatives.id', 'planning_assignments.initiative_id')
            ->join('users', 'users.id', 'planning_assignments.user_id')
            ->whereBetween('plannings.planning_date', [$startWeekDate, $endWeekDate])
            ->orderBy('id')
            ->get();

        $planningUsersRows = [];
        $groupByPlanningInitiative = $plannings->groupBy('initiative_id');
        foreach ($groupByPlanningInitiative as $groupByPlanningInitiativeKey => $groupByPlanningInitiativeValue) {
            $initiativeData = $groupByPlanningInitiativeValue->firstWhere('initiative_id', $groupByPlanningInitiativeKey);

            $initiativeUsersArray = [];
            $groupByPlanningInitiativeUsers = $groupByPlanningInitiativeValue->groupBy('user_id');
            foreach ($groupByPlanningInitiativeUsers as $groupByPlanningInitiativeUsersKey => $groupByPlanningInitiativeUsersValue) {
                $userData = $groupByPlanningInitiativeUsersValue->firstWhere('user_id', $groupByPlanningInitiativeUsersKey);
                $userHoursPerWeek = [];
                foreach ($loadWeeks as $loadWeek) {
                    $userHoursPerWeek[$loadWeek['date']] = [
                        'hours' => $groupByPlanningInitiativeUsersValue->where('week', $loadWeek['week'])->sum('hours')
                    ];
                }
                $initiativeUsersArray[] = [
                    'id' => $userData->user_id,
                    'name' => $userData->user_name,
                    'hours_per_week' => $userHoursPerWeek,
                ];
            }
            $planningUsersRows[] = [
                'default_row_name' => '',
                'initiative_id' => $initiativeData->id,
                'initiative_name' => $initiativeData->initiative_name,
                'users' => $initiativeUsersArray,
            ];
        }

        $userArray = [
            'name' => '',
            'id' => '',
            'hours_per_week' => []
        ];

        $planningTotalRow = [
            'default_row_name' => 'heder_total',
            'initiative_id' => '',
            'initiative_name' => 'Total',
            'users' => [$userArray],
        ];

        $planNewInitiativesRow = [
            'default_row_name' => 'plan_new_initiative',
            'initiative_id' => '',
            'initiative_name' => 'Plan New Initiative',
            'users' => [$userArray],
        ];

        $planningTotalRowHoursPerWeek = [];
        $planningNewInitiativeRowHoursPerWeek = [];
        foreach ($loadWeeks as $loadWeek) {
            $planningTotalRowHoursPerWeek[$loadWeek['date']] = [
                'hours' => $plannings->where('week', $loadWeek['week'])->sum('hours'),
            ];
            $planningNewInitiativeRowHoursPerWeek[$loadWeek['date']] = [
                'hours' => 0
            ];
        }
        $planningTotalRow['users'][0]['hours_per_week'] = $planningTotalRowHoursPerWeek;
        $planNewInitiativesRow['users'][0]['hours_per_week'] = $planningNewInitiativeRowHoursPerWeek;

        $planningData = $planningUsersRows;

        // array_push($planningData, $planningTotalRow);
        array_unshift($planningData, $planningTotalRow);
        array_push($planningData, $planNewInitiativesRow);

        $retData = [
            'plannings' => $planningData,
            'loadWeeks' => $loadWeeks
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }
    public function getPlanningInitialData(Request $request)
    {
        $retData = [
            'initiatives' => PlanningService::getInitiatives(),
            'users' => PlanningService::getUsers(),
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }
}

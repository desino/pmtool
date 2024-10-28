<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Models\Planning;
use App\Models\PlanningAssignment;
use App\Services\InitiativeService;
use App\Services\PlanningService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlanningController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.planning.dont_have_permission'), null, 404);
        }
        $requestData = $request->all();
        $currentWeek = Carbon::now()->startOfWeek();
        $startWeek = "";
        $endWeek = "";
        $startWeekDate = "";
        $endWeekDate = "";

        // $startWeek = Carbon::now()->subWeek(4)->startOfWeek();
        $startWeek = Carbon::now()->startOfWeek();
        $endWeek = Carbon::now()->addWeek(8)->startOfWeek();
        if ($request->get('previous_or_next_of_week') == '-1') {
            $startWeek = Carbon::parse($request->get('start_date'))->subWeek(4)->startOfWeek();
            $endWeek = Carbon::parse($request->get('end_date'))->startOfWeek();
        } else if ($request->get('previous_or_next_of_week') == '1') {
            $startWeek = Carbon::parse($request->get('start_date'))->startOfWeek();
            $endWeek = Carbon::parse($request->get('end_date'))->addWeek(4)->startOfWeek();
        } else if ($request->get('previous_or_next_of_week') == '0' && $request->get('start_date') && $request->get('end_date')) {
            $startWeek = Carbon::parse($request->get('start_date'))->startOfWeek();
            $endWeek = Carbon::parse($request->get('end_date'))->startOfWeek();
        }
        $startWeekDate = $startWeek->toDateString();
        $endWeekDate = $endWeek->toDateString();

        $loadWeeks = [];
        while ($startWeek->lte($endWeek)) {
            $loadWeeks[] = [
                'date' => $startWeek->toDateString(),
                'week' => $startWeek->weekOfYear,
                'display_week_name' => $startWeek->format('d/m'),
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
                    $hour = $groupByPlanningInitiativeUsersValue->where('planning_date', $loadWeek['date'])->sum('hours');
                    $userHoursPerWeek[$loadWeek['date']] = [
                        'hours' => $hour > 0 ? $hour : '',
                    ];
                }
                $initiativeUsersArray[] = [
                    'id' => $userData->user_id,
                    'name' => $userData->user_name,
                    'hours_per_week' => $userHoursPerWeek,
                ];
            }

            $userHoursPerWeekForPlanningAddNewUserRow = [];
            foreach ($loadWeeks as $loadWeek) {
                $userHoursPerWeekForPlanningAddNewUserRow[$loadWeek['date']] = [
                    'hours' => ''
                ];
            }
            $planningAddNewUserRow = [
                'id' => '',
                'name' => '<i class="bi bi-plus-circle"></i>',
                'hours_per_week' => $userHoursPerWeekForPlanningAddNewUserRow,
            ];
            // array_push($initiativeUsersArray, $planningAddNewUserRow);            
            $planningUsersRows[] = [
                'default_row_name' => '',
                'initiative_id' => $initiativeData->initiative_id,
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
                'hours' => $plannings->where('planning_date', $loadWeek['date'])->sum('hours'),
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

    public function storePlanning(Request $request)
    {
        $requestData = $request->all();
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.planning.dont_have_permission'), null, 404);
        }
        $initiativeIds = array_unique(array_column($requestData, 'initiative_id'));

        $initiatives = Initiative::whereIn('id', $initiativeIds)->get();
        if ($initiatives->count() != count($initiativeIds)) {
            return ApiHelper::response(false, __('messages.planning.initiative_not_found'), '', 400);
        }
        $status = true;
        $message = __('messages.planning.create_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            foreach ($requestData as $planning) {
                $planningAssignmentUpdateCondition = [
                    'initiative_id' => $planning['initiative_id'],
                    'user_id' => $planning['user_id']
                ];
                $planningAssignmentInsertData = [
                    'initiative_id' => $planning['initiative_id'],
                    'user_id' => $planning['user_id'],
                ];
                $planningAssignment = PlanningAssignment::updateOrCreate(
                    $planningAssignmentUpdateCondition,
                    $planningAssignmentInsertData
                );

                $deletePlanningIds = [];
                foreach ($planning['hours_per_week'] as $week) {
                    $planning = Planning::updateOrCreate(
                        [
                            'planning_assignment_id' => $planningAssignment->id,
                            'planning_date' => $week['date']
                        ],
                        [
                            'planning_assignment_id' => $planningAssignment->id,
                            'planning_date' => $week['date'],
                            'hours' => $week['hours']
                        ]
                    );
                    $deletePlanningIds[] = $planning->id;
                }
                if (count($deletePlanningIds) > 0) {
                    Planning::whereNotIn('id', $deletePlanningIds)->where('planning_assignment_id', $planningAssignment->id)->delete();
                }
            }
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

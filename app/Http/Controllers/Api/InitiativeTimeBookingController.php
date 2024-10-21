<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AssignProjectForInitiativeTimeBookingRequest;
use App\Models\Initiative;
use App\Models\Project;
use App\Models\TimeBooking;
use App\Models\User;
use App\Services\InitiativeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Type\Time;

class InitiativeTimeBookingController extends Controller
{
    public function index(Request $request)
    {
        $requestData = $request->all();
        $filter = $requestData['filter'];
        $defaultDays = Config::get('myapp.initiative_time_booking_load_default_data_days');
        if (isset($filter['days']) && $filter['days'] != '') {
            $defaultDays = (int) $filter['days'];
        }

        $currentDate = Carbon::now();
        $startDate = $currentDate->copy()->subDays($defaultDays)->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');

        $timeBookings = TimeBooking::select(
            '*',
            DB::RAW('false as is_checked')
        )
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name');
                },
                'initiative' => function ($query) {
                    $query->select('id', 'name');
                },
                'project' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->whereBetween('booked_date', [$startDate, $endDate])
            ->where('initiative_id', '!=', '-1')
            ->when($filter['initiative_id'] != '', function ($query) use ($filter) {
                $query->where('initiative_id', $filter['initiative_id']);
            })
            ->when($filter['user_id'] != '', function ($query) use ($filter) {
                $query->where('user_id', $filter['user_id']);
            })
            ->when($filter['project_id'] != '' && $filter['include_mapped'] == 'true', function ($query) use ($filter) {
                $query->where('project_id', $filter['project_id']);
            })
            ->when($filter['include_mapped'] == 'false', function ($query) use ($filter) {
                $query->whereNull('project_id');
            })
            ->paginate(50);
        return ApiHelper::response(true, '', $timeBookings, 200);
    }

    public function getInitialDataForInitiativeTimeBookings(Request $request)
    {
        $initiatives = Initiative::get();
        $users = User::get();
        $retData = [
            'initiatives' => $initiatives,
            'users' => $users
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }

    public function getProjectListForInitiativeTimeBookings(Request $request)
    {
        $projectList = Project::where('initiative_id', $request->initiative_id)->get();
        return ApiHelper::response(true, '', $projectList, 200);
    }

    public function assignProjectForInitiativeTimeBookings(AssignProjectForInitiativeTimeBookingRequest $request)
    {
        $status = false;
        $requestData = $request->all();

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $project = Project::where('initiative_id', $requestData['initiative_id'])->find($requestData['project_id']);
        if (!$project) {
            return ApiHelper::response($status, __('messages.project.not_found'), '', 400);
        }

        $status = true;
        $message = __('messages.project.update_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            DB::commit();
            TimeBooking::whereIn('id', $requestData['time_booking_ids'])
                ->where('initiative_id', $requestData['initiative_id'])
                ->update([
                    'project_id' => $requestData['project_id']
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, null, $statusCode);
    }
}

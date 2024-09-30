<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TimeBookingRequest;
use App\Models\Initiative;
use App\Models\Ticket;
use App\Models\TimeBooking;
use App\Services\InitiativeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimeBookingController extends Controller
{
    public function index(Request $request)
    {
        $retData = [
            'weekDays' => [],
            'initiativeWithTicketsAndTimeBooking' => [],
            'thRowSpanCount' => 0
        ];
        $currentStartDate = Carbon::now()->startOfWeek();
        $otherStartDate = "";

        $startOfWeekDate = Carbon::now()->startOfWeek();
        $startOfWeek =  $startOfWeekDate->format('Y-m-d');
        $endOfWeekDate = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        $endOfWeek = $endOfWeekDate->format('Y-m-d');
        $todayDate = Carbon::now()->toDateString();
        if ($request->get('previous_or_next_of_week') == '-1') {
            $startOfWeekDate = Carbon::parse($request->get('start_date'))->subWeek()->startOfWeek();
            $startOfWeek =  $startOfWeekDate->format('Y-m-d');
            $endOfWeekDate = Carbon::parse($request->get('end_date'))->endOfWeek(Carbon::SUNDAY);
            $endOfWeek = $endOfWeekDate->format('Y-m-d');
            // $otherStartDate = Carbon::parse($startOfWeek)->startOfWeek();
        } else if ($request->get('previous_or_next_of_week') == '1') {
            $startOfWeekDate = Carbon::parse($request->get('start_date'))->startOfWeek();
            $startOfWeek =  $startOfWeekDate->format('Y-m-d');
            $endOfWeekDate = Carbon::parse($request->get('end_date'))->addWeek()->endOfWeek(Carbon::SUNDAY);
            $endOfWeek = $endOfWeekDate->format('Y-m-d');
            $otherStartDate = Carbon::parse($endOfWeek)->startOfWeek();
        }

        $weekDifferenceCount = abs($currentStartDate->diffInWeeks($otherStartDate));
        if ($weekDifferenceCount == 1) {
            return ApiHelper::response(false, __('message.time_booking.only_one_week'), '', 400);
        }


        $weekDays = [];
        while ($startOfWeekDate->lte($endOfWeek)) {
            $weekDays[] = [
                'day' => $startOfWeekDate->format('l'),
                'date' => $startOfWeekDate->toDateString(),
                'format_date' => $startOfWeekDate->format('d/m'),
                'is_today' => $startOfWeekDate->toDateString() == $todayDate,
                'total_hours' => 0
            ];
            $startOfWeekDate->addDay();
        }
        $retData['weekDays'] = $weekDays;

        $initiative = Initiative::select(
            'initiatives.id',
            'initiatives.name'
        )
            ->with([
                'timeBookings' => function ($q) use ($startOfWeek, $endOfWeek) {
                    $q->select(
                        'initiative_id',
                        'booked_date',
                        DB::RAW('SUM(hours) as duration'),
                    )
                        ->where('user_id', Auth::id())
                        ->whereBetween('booked_date', [$startOfWeek, $endOfWeek])
                        ->groupBy('initiative_id')
                        ->groupBy('booked_date');
                },
                'timeBookingsWithoutTickets' => function ($q) use ($startOfWeek, $endOfWeek) {
                    $q->select(
                        'initiative_id',
                        'booked_date',
                        DB::RAW('SUM(hours) as duration'),
                    )
                        ->where('user_id', Auth::id())
                        ->whereBetween('booked_date', [$startOfWeek, $endOfWeek])
                        ->groupBy('initiative_id')
                        ->groupBy('booked_date');
                },
                'tickets' => function ($q) use ($startOfWeek, $endOfWeek) {
                    $q->select(
                        'id',
                        'initiative_id',
                        'name',
                        'composed_name',
                    )
                        ->with([
                            'timeBookings' => function ($query) use ($startOfWeek, $endOfWeek) {
                                $query->select(
                                    'ticket_id',
                                    'booked_date',
                                    DB::RAW('SUM(hours) as duration')
                                )
                                    ->where('user_id', Auth::id())
                                    ->whereBetween('booked_date', [$startOfWeek, $endOfWeek])
                                    ->groupBy('ticket_id')
                                    ->groupBy('booked_date');
                            }
                        ])
                        ->whereHas('actions', function ($query) {
                            $query->where('user_id', Auth::id());
                        });
                },
            ])
            ->where(function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereHas('tickets', function ($query) {
                    $query->where('tickets.status', '!=', Ticket::getStatusDone())
                        ->whereHas('actions', function ($query) {
                            $query->where('user_id', Auth::id());
                        });
                })
                    ->orWhereHas('timeBookings', function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->where('user_id', Auth::id())
                            ->whereBetween('booked_date', [$startOfWeek, $endOfWeek]);
                    });
            })
            ->orderBy('initiatives.id')
            ->get();
        $initiativeCount = $initiative->count();
        $initiativeData = [];
        $totalTicketCount = 0;
        foreach ($initiative as $initiative) {
            $row = [
                'initiative_id' => $initiative->id,
                'initiative_name' => $initiative->name,
                'initiative_booking' => [],
                'tickets' => [],
            ];
            $initiativeLevelBookingRowData = [
                'ticket_id' => '',
                'ticket_name' => __('messages.time_booking.list_table.initiative_level_booking'),
                'hours_per_day' => "",
            ];
            $initiativeLevelBookingHoursPerDay = [];

            foreach ($weekDays as $weekDay) {
                $initiativeTimeBookingBooked = $initiative->timeBookingsWithoutTickets->firstWhere('booked_date', $weekDay['date']);
                $initiativeLevelBookingHoursPerDay[$weekDay['date']] = $initiativeTimeBookingBooked ? $initiativeTimeBookingBooked->duration : '';
            }
            $initiativeLevelBookingRowData['hours_per_day'] = $initiativeLevelBookingHoursPerDay;
            $row['tickets'][] = $initiativeLevelBookingRowData;

            $totalTicketCount += $initiative->tickets->count();
            $initiativeLevelHours = [];
            foreach ($weekDays as $weekDay) {
                $booked = $initiative->timeBookings->firstWhere('booked_date', $weekDay['date']);
                $hours = $booked ? $booked->duration : 0;
                $initiativeLevelHours[$weekDay['date']] = $hours;
                $retData['weekDays'][array_search($weekDay, $weekDays)]['total_hours'] += $hours;
            }
            $row['initiative_booking'] = $initiativeLevelHours;
            foreach ($initiative->tickets as $ticketKey => $ticket) {
                $ticketRow = [
                    'ticket_id' => $ticket->id,
                    'ticket_name' => $ticket->name,
                    // 'daily_totals' => array_fill_keys(array_keys($weekDays), 0),
                ];
                $hoursPerDay = [];
                foreach ($weekDays as $weekDay) {
                    $booked = $ticket->timeBookings->firstWhere('booked_date', $weekDay['date']);
                    $hoursPerDay[$weekDay['date']] = $booked ? $booked->duration : '';
                }
                $ticketRow['hours_per_day'] = $hoursPerDay;
                $row['tickets'][] = $ticketRow;
            }
            $initiativeData[] = $row;
        }
        $retData['thRowSpanCount'] = $totalTicketCount + $initiativeCount + 1;
        $retData['initiativeWithTicketsAndTimeBooking'] = $initiativeData;
        return ApiHelper::response(true, '', $retData, 200);
    }

    public function getTimeBookingModalInitialData(Request $request)
    {
        $requestData = $request->all();
        $status = false;
        $initiative = InitiativeService::getInitiative($request, $requestData['initiative_id']);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        if ($requestData['ticket_id'] != '') {
            $ticket = Ticket::find($requestData['ticket_id']);
            if (!$ticket || $initiative->id != $ticket->initiative_id) {
                return ApiHelper::response($status, __('messages.ticket.not_found'), '', 400);
            }
        }

        $status = true;
        $statusCode = 200;
        $timeBookings = TimeBooking::select(
            'id',
            'booked_date',
            'comments',
            'hours',
            DB::RAW('false as is_checked')
        )
            ->where('user_id', Auth::id())
            ->where('initiative_id', $requestData['initiative_id'])
            ->where('ticket_id', $requestData['ticket_id'])
            ->whereDate('booked_date', $requestData['booked_date'])
            ->orderBy('created_at')
            ->get();
        $data = [
            'timeBookings' => $timeBookings,
            'totalTimeBookingHours' => $timeBookings->sum('hours'),
        ];
        return ApiHelper::response($status, '', $data, $statusCode);
    }

    public function store(TimeBookingRequest $request)
    {
        $validData = $request->validated();
        $validData['user_id'] = Auth::id();
        $status = false;

        if ($validData['booked_date'] > Carbon::now()->format('Y-m-d')) {
            return ApiHelper::response($status, __('messages.time_booking.booked_date_error'), '', 400);
        }

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        if ($validData['ticket_id'] != '') {
            $ticket = Ticket::find($validData['ticket_id']);
            if (!$ticket || $initiative->id != $ticket->initiative_id) {
                return ApiHelper::response($status, __('messages.ticket.not_found'), '', 400);
            }
        }
        DB::beginTransaction();
        $message = __('messages.time_booking.store_success');
        $statusCode = 200;
        try {
            TimeBooking::create($validData);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }

    public function deleteTimeBookings(Request $request)
    {
        $requestData = $request->all();
        $timeBookings = TimeBooking::whereIn('id', $requestData['timeBookingIds'])->get();
        if ($timeBookings->count() == 0) {
            return ApiHelper::response(false, __('messages.time_booking.not_found'), '', 400);
        }
        $status = false;
        $message = __('messages.time_booking.delete_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            TimeBooking::whereIn('id', $requestData['timeBookingIds'])->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }
}

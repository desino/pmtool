<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TimeBookingOnNewInitiativeOrTicketRequest;
use App\Http\Requests\Api\TimeBookingOnNewTicketRequest;
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
                'format_date_dd_mm_yyyy' => $startOfWeekDate->format('d/m/Y'),
                'is_today' => $startOfWeekDate->toDateString() == $todayDate,
                'total_hours' => 0
            ];
            $startOfWeekDate->addDay();
        }
        $retData['weekDays'] = $weekDays;


        $timeBookings = TimeBooking::select(
            'time_bookings.booked_date',
            'time_bookings.hours',
            'initiatives.id as initiative_id',
            'initiatives.name as initiative_name',
            'tickets.id as ticket_id',
            'tickets.composed_name as ticket_name',
        )
            ->JOIN('initiatives', 'initiatives.id', 'time_bookings.initiative_id')
            ->LEFTJOIN('tickets', 'tickets.id', 'time_bookings.ticket_id')
            ->where('user_id', Auth::id())
            ->whereBetween('booked_date', [$startOfWeek, $endOfWeek])
            ->get();

        $defaultRowData = [
            'initiative_id' => '',
            'initiative_name' => __('messages.time_booking.list_table.book_on_new_initiative_or_ticket'),
            'tickets' => [
                [
                    'ticket_id' => '',
                    'ticket_name' => '',
                    'hours_per_day' => [],
                ]
            ],
        ];
        $defaultRowDataTicketsHoursPerDay = [];
        foreach ($weekDays as $weekDay) {
            $defaultRowDataTicketsHoursPerDay[$weekDay['date']]['hours'] = '<i class="bi bi-plus-circle"></i>';
            $defaultRowDataTicketsHoursPerDay[$weekDay['date']]['is_allow_booking'] = $weekDay['date'] <= Carbon::now()->format('Y-m-d') ?? false;
            $retData['weekDays'][array_search($weekDay, $weekDays)]['total_hours'] = $timeBookings->where('booked_date', $weekDay['date'])->sum('hours');
        }
        $defaultRowData['tickets'][0]['hours_per_day'] = $defaultRowDataTicketsHoursPerDay;

        $timeBookingRowData = [];
        $initiativeGroupByTimeBooking = $timeBookings->groupBy('initiative_id');
        foreach ($initiativeGroupByTimeBooking as $timeBookingKey => $timeBooking) {
            $timeBookingFirst = $timeBooking->first();
            $row = [
                'initiative_id' => $timeBookingFirst->initiative_id,
                'initiative_name' => $timeBookingFirst->initiative_name,
                'tickets' => [],
            ];

            $initiativeTimeBookings = $timeBooking->whereNull('ticket_id');
            $initiativeLevelBookingRowData = [
                'ticket_id' => '',
                'ticket_name' => __('messages.time_booking.list_table.initiative_level_booking'),
                'hours_per_day' => [],
            ];
            $initiativeLevelBookingHoursPerDay = [];
            foreach ($weekDays as $weekDay) {
                $initiativeLevelBookingHoursPerDay[$weekDay['date']]['hours'] = $initiativeTimeBookings->Where('booked_date', $weekDay['date'])->sum('hours') ?? '';
                $initiativeLevelBookingHoursPerDay[$weekDay['date']]['is_allow_booking'] = $weekDay['date'] <= Carbon::now()->format('Y-m-d') ?? false;
            }
            $initiativeLevelBookingRowData['hours_per_day'] = $initiativeLevelBookingHoursPerDay;


            $initiativeTickets = [];
            $initiativeTimeBookingsTickets = $timeBooking->whereNotNull('ticket_id')->groupBy('ticket_id');
            foreach ($initiativeTimeBookingsTickets as $initiativeTimeBookingTicket) {
                $initiativeTicketsHoursPerDay = [];
                foreach ($weekDays as $weekDay) {
                    $initiativeTicketsHoursPerDay[$weekDay['date']]['hours'] = $initiativeTimeBookingTicket->Where('booked_date', $weekDay['date'])->sum('hours') ?? '';
                    $initiativeTicketsHoursPerDay[$weekDay['date']]['is_allow_booking'] = $weekDay['date'] <= Carbon::now()->format('Y-m-d') ?? false;
                }
                $initiativeTimeBookingTicketFirst = $initiativeTimeBookingTicket->first();
                $initiativeTickets[] = [
                    'ticket_id' => $initiativeTimeBookingTicketFirst->ticket_id,
                    'ticket_name' => $initiativeTimeBookingTicketFirst->ticket_name,
                    'hours_per_day' => $initiativeTicketsHoursPerDay,
                ];
            }
            array_unshift($initiativeTickets, $initiativeLevelBookingRowData);

            $initiativeLevelTicketBookingRowData = [
                'ticket_id' => '',
                'ticket_name' => __('messages.time_booking.list_table.book_on_new_ticket'),
                'hours_per_day' => [],
            ];
            $initiativeLevelTicketBookingHoursPerDay = [];
            foreach ($weekDays as $weekDay) {
                $initiativeLevelTicketBookingHoursPerDay[$weekDay['date']]['hours'] = '';
                $initiativeLevelTicketBookingHoursPerDay[$weekDay['date']]['is_allow_booking'] = $weekDay['date'] <= Carbon::now()->format('Y-m-d') ?? false;
            }
            $initiativeLevelTicketBookingRowData['hours_per_day'] = $initiativeLevelTicketBookingHoursPerDay;
            array_push($initiativeTickets, $initiativeLevelTicketBookingRowData);
            $row['tickets'] = $initiativeTickets;
            $timeBookingRowData[] = $row;
        }
        array_push($timeBookingRowData, $defaultRowData);
        $retData['thRowSpanCount'] = 0;
        $retData['initiativeWithTicketsAndTimeBooking'] = $timeBookingRowData;
        return ApiHelper::response(true, '', $retData, 200);



        // =============== OLD logic for a time booking ==============
        // $initiative = Initiative::select(
        //     'initiatives.id',
        //     'initiatives.name'
        // )
        //     ->with([
        //         'timeBookings' => function ($q) use ($startOfWeek, $endOfWeek) {
        //             $q->select(
        //                 'initiative_id',
        //                 'booked_date',
        //                 DB::RAW('SUM(hours) as duration'),
        //             )
        //                 ->where('user_id', Auth::id())
        //                 ->whereBetween('booked_date', [$startOfWeek, $endOfWeek])
        //                 ->groupBy('initiative_id')
        //                 ->groupBy('booked_date');
        //         },
        //         'timeBookingsWithoutTickets' => function ($q) use ($startOfWeek, $endOfWeek) {
        //             $q->select(
        //                 'initiative_id',
        //                 'booked_date',
        //                 DB::RAW('SUM(hours) as duration'),
        //             )
        //                 ->where('user_id', Auth::id())
        //                 ->whereBetween('booked_date', [$startOfWeek, $endOfWeek])
        //                 ->groupBy('initiative_id')
        //                 ->groupBy('booked_date');
        //         },
        //         'tickets' => function ($q) use ($startOfWeek, $endOfWeek) {
        //             $q->select(
        //                 'id',
        //                 'initiative_id',
        //                 'name',
        //                 'composed_name',
        //             )
        //                 ->with([
        //                     'timeBookings' => function ($query) use ($startOfWeek, $endOfWeek) {
        //                         $query->select(
        //                             'ticket_id',
        //                             'booked_date',
        //                             DB::RAW('SUM(hours) as duration')
        //                         )
        //                             ->where('user_id', Auth::id())
        //                             ->whereBetween('booked_date', [$startOfWeek, $endOfWeek])
        //                             ->groupBy('ticket_id')
        //                             ->groupBy('booked_date');
        //                     }
        //                 ])
        //                 ->whereHas('actions', function ($query) {
        //                     $query->where('user_id', Auth::id());
        //                 });
        //         },
        //     ])
        //     ->where(function ($query) use ($startOfWeek, $endOfWeek) {
        //         $query->whereHas('tickets', function ($query) {
        //             $query->where('tickets.status', '!=', Ticket::getStatusDone())
        //                 ->whereHas('actions', function ($query) {
        //                     $query->where('user_id', Auth::id());
        //                 });
        //         })
        //             ->orWhereHas('timeBookings', function ($query) use ($startOfWeek, $endOfWeek) {
        //                 $query->where('user_id', Auth::id())
        //                     ->whereBetween('booked_date', [$startOfWeek, $endOfWeek]);
        //             });
        //     })
        //     ->orderBy('initiatives.id')
        //     ->get();
        // $initiativeCount = $initiative->count();
        // $initiativeData = [];
        // $totalTicketCount = 0;
        // foreach ($initiative as $initiative) {
        //     $row = [
        //         'initiative_id' => $initiative->id,
        //         'initiative_name' => $initiative->name,
        //         'initiative_booking' => [],
        //         'tickets' => [],
        //     ];

        //     $totalTicketCount += $initiative->tickets->count();
        //     $initiativeLevelHours = [];
        //     foreach ($weekDays as $weekDay) {
        //         $booked = $initiative->timeBookings->firstWhere('booked_date', $weekDay['date']);
        //         $hours = $booked ? $booked->duration : 0;
        //         $initiativeLevelHours[$weekDay['date']] = $hours;
        //         $retData['weekDays'][array_search($weekDay, $weekDays)]['total_hours'] += $hours;
        //     }
        //     $row['initiative_booking'] = $initiativeLevelHours;

        //     $initiativeLevelBookingRowData = [
        //         'ticket_id' => '',
        //         'ticket_name' => __('messages.time_booking.list_table.initiative_level_booking'),
        //         'hours_per_day' => [],
        //     ];
        //     $initiativeLevelBookingHoursPerDay = [];

        //     foreach ($weekDays as $weekDay) {
        //         $initiativeTimeBookingBooked = $initiative->timeBookingsWithoutTickets->firstWhere('booked_date', $weekDay['date']);
        //         $initiativeLevelBookingHoursPerDay[$weekDay['date']]['hours'] = $initiativeTimeBookingBooked ? $initiativeTimeBookingBooked->duration : '';
        //         $initiativeLevelBookingHoursPerDay[$weekDay['date']]['is_allow_booking'] = $weekDay['date'] <= Carbon::now()->format('Y-m-d') ?? false;
        //     }
        //     $initiativeLevelBookingRowData['hours_per_day'] = $initiativeLevelBookingHoursPerDay;
        //     $row['tickets'][] = $initiativeLevelBookingRowData;

        //     foreach ($initiative->tickets as $ticketKey => $ticket) {
        //         $ticketRow = [
        //             'ticket_id' => $ticket->id,
        //             'ticket_name' => $ticket->name,
        //             'hours_per_day' => [],
        //         ];
        //         $hoursPerDay = [];
        //         foreach ($weekDays as $weekDay) {
        //             $booked = $ticket->timeBookings->firstWhere('booked_date', $weekDay['date']);
        //             $hoursPerDay[$weekDay['date']]['hours'] = $booked ? $booked->duration : '';
        //             $hoursPerDay[$weekDay['date']]['is_allow_booking'] = $weekDay['date'] <= Carbon::now()->format('Y-m-d') ?? false;
        //         }
        //         $ticketRow['hours_per_day'] = $hoursPerDay;
        //         $row['tickets'][] = $ticketRow;
        //     }
        //     $initiativeData[] = $row;
        // }
        // $retData['thRowSpanCount'] = $totalTicketCount + $initiativeCount + 1;
        // $retData['initiativeWithTicketsAndTimeBooking'] = $initiativeData;
        // return ApiHelper::response(true, '', $retData, 200);
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
            'initiative_id',
            'ticket_id',
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
    public function getTimeBookingOnNewTicketModalInitialData(Request $request)
    {
        $requestData = $request->all();
        $status = false;
        $initiative = InitiativeService::getInitiative($request, $requestData['initiative_id']);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $status = true;
        $statusCode = 200;
        // $date = Carbon::parse($requestData['booked_date']);
        // $startOfWeek = $date->copy()->startOfWeek()->format('Y-m-d');
        // $endOfWeek = $date->copy()->endOfWeek()->format('Y-m-d');

        $startDate = Carbon::parse($requestData['start_date'])->format('Y-m-d');
        $endDate = Carbon::parse($requestData['end_date'])->format('Y-m-d');
        $tickets = Ticket::where('initiative_id', $requestData['initiative_id'])
            ->whereHas('actions', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->whereDoesntHave('timeBookings', function ($query) use ($requestData, $startDate, $endDate) {
                $query->where('initiative_id', $requestData['initiative_id'])
                    ->whereNotNull('ticket_id')
                    ->whereBetween('booked_date', [$startDate, $endDate]);
            })
            ->get();

        $data = [
            'tickets' => $tickets,
        ];
        return ApiHelper::response($status, '', $data, $statusCode);
    }

    public function getTimeBookingOnNewInitiativeOrTicketModalInitialData(Request $request)
    {
        $requestData = $request->all();
        $status = false;

        $status = true;
        $statusCode = 200;

        // $date = Carbon::parse($requestData['booked_date']);
        // $startOfWeek = $date->copy()->startOfWeek()->format('Y-m-d');
        // $endOfWeek = $date->copy()->endOfWeek()->format('Y-m-d');

        $startDate = Carbon::parse($requestData['start_date'])->format('Y-m-d');
        $endDate = Carbon::parse($requestData['end_date'])->format('Y-m-d');
        $initiatives = Initiative::whereDoesntHave('timeBookings', function ($query) use ($startDate, $endDate) {
            $query->whereNotNull('initiative_id')
                ->whereBetween('booked_date', [$startDate, $endDate]);
        })
            ->get();

        $data = [
            'initiatives' => $initiatives,
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

    public function storeTimeBookingOnNewTicket(TimeBookingOnNewTicketRequest $request)
    {
        $validData = $request->validated();
        $validData['user_id'] = Auth::id();

        $status = false;
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        $ticket = Ticket::find($validData['ticket_id']);
        if (!$ticket || $initiative->id != $ticket->initiative_id) {
            return ApiHelper::response($status, __('messages.ticket.not_found'), '', 400);
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

    public function storeTimeBookingOnNewInitiativeOrTicket(TimeBookingOnNewInitiativeOrTicketRequest $request)
    {
        $validData = $request->validated();
        $validData['user_id'] = Auth::id();
        $status = false;
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

    public function fetchTickets(Request $request)
    {
        $requestData = $request->all();

        $status = false;
        $initiative = InitiativeService::getInitiative($request, $requestData['initiative_id']);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $startDate = Carbon::parse($requestData['start_date'])->format('Y-m-d');
        $endDate = Carbon::parse($requestData['end_date'])->format('Y-m-d');

        $status = true;
        $statusCode = 200;
        $tickets = Ticket::where('initiative_id', $requestData['initiative_id'])
            ->whereHas('actions', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->whereDoesntHave('timeBookings', function ($query) use ($requestData, $startDate, $endDate) {
                $query->where('initiative_id', $requestData['initiative_id'])
                    ->whereNotNull('ticket_id')
                    ->whereBetween('booked_date', [$startDate, $endDate]);
            })
            ->get();
        $data = [
            'tickets' => $tickets,
        ];
        return ApiHelper::response($status, '', $data, $statusCode);
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

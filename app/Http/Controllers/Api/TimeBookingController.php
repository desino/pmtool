<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Models\Ticket;
use App\Models\TimeBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimeBookingController extends Controller
{
    public function index(Request $request)
    {
        $retData = [
            'weekDays' => [],
            'initiativeWithTicketsAndTimeBooking' => [],
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
                        DB::RAW('SUM(hours) as duration')
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

        $initiativeData = [];
        foreach ($initiative as $initiative) {
            $row = [
                'initiative_name' => $initiative->name,
                'initiative_booking' => [],
                'tickets' => [],
            ];
            $initiativeLevelHours = [];
            foreach ($weekDays as $weekDay) {
                $booked = $initiative->timeBookings->firstWhere('booked_date', $weekDay['date']);
                $hours = $booked ? $booked->duration : 0;
                $initiativeLevelHours[$weekDay['date']] = $hours;
                $retData['weekDays'][array_search($weekDay, $weekDays)]['total_hours'] += $hours;
            }
            $row['initiative_booking'] = $initiativeLevelHours;
            foreach ($initiative->tickets as $ticket) {
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
        $retData['initiativeWithTicketsAndTimeBooking'] = $initiativeData;
        return ApiHelper::response(true, '', $retData, 200);
    }
}

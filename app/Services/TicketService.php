<?php

namespace App\Services;

use App\Models\Initiative;
use App\Models\Ticket;
use Carbon\Carbon;

class TicketService
{
    public static function generateTicketComposedName(int $initiativeId, string $name, int $type)
    {
        $typeCode = Ticket::getTypeOfCode($type);
        $initiative = Initiative::find($initiativeId);
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->format('m');
        $ticketCount = 0;

        $lastTicket = Ticket::whereHas('functionality.section.initiative', function ($q) use ($initiative) {
            $q->where('id', $initiative->id);
        })
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$lastTicket) {
            $ticketCount = 1;
        } else {
            $lastTicketCreatedMonth = Carbon::parse($lastTicket->created_at)->format('m');
            if ($lastTicketCreatedMonth != $currentMonth) {
                $initiative->ticket_composed_name_count = 0;
                $initiative->save();
                $ticketCount = 1;
            } else {
                $ticketCount = $initiative->ticket_composed_name_count + 1;
            }
        }
        $composedName = $typeCode . $currentYear . $currentMonth . '-' . $ticketCount . ':' . $name;
        $retData = [
            'composed_name' => $composedName,
        ];
        return $retData;
    }

    public static function incrementInitiativeTicketCount(int $initiativeId)
    {
        $initiative = Initiative::find($initiativeId);
        $initiative->ticket_composed_name_count = $initiative->ticket_composed_name_count + 1;
        $initiative->save();
    }
}

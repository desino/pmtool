<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TitcketRequest;
use App\Models\Section;
use App\Models\Ticket;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function getSectionFunctionality(Request $request)
    {
        $sectionFunctionalities = Section::select(
            'id',
            'name',
        )
            ->with(['functionalities' => function ($q) {
                $q->select(
                    'id',
                    'section_id',
                    'name',
                    'id'
                );
            }])
            ->where('initiative_id', $request->post('initiative_id'))
            ->get();
        return ApiHelper::response(true, '', $sectionFunctionalities, 200);
    }

    public function store(TitcketRequest $request)
    {
        $validatData = $request->validated();
        $status = false;
        $retData = [
            'ticket' => "",
        ];
        DB::beginTransaction();
        try {
            $ticket = Ticket::create($validatData);
            $status = true;
            $meesage = __('messages.create_ticket.store_success');
            $statusCode = 200;
            $retData = [
                'ticket' => $ticket,
            ];
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $meesage, $retData, $statusCode);
    }

    public function show($id)
    {
        $ticket = Ticket::with('functionality')->find($id);

        if (!$ticket) {
            return ApiHelper::response('false', __('messages.ticket.not_found'), [], 404);
        }

        return ApiHelper::response(true, __('messages.ticket.fetched'), $ticket, 200);
    }

    public function allTicketsForDropdown()
    {
        $tickets = Ticket::query()->get(['id','name']);

        if ($tickets->isEmpty()) {
            return ApiHelper::response('false', __('messages.ticket.not_found'), [], 404);
        }

        return ApiHelper::response(true, __('messages.ticket.fetched'), $tickets, 200);

    }
}

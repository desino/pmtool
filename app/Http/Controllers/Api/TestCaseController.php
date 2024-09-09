<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\TestCase;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestCaseController extends Controller
{
    /**
     * Store a new TestCase for the given ticket.
     *
     * @param Request $request
     * @param int $ticket_id
     * @return JsonResponse
     */
    public function store(Request $request, int $ticket_id): JsonResponse
    {
        // Check if the ticket exists
        $ticketData = Ticket::find($ticket_id);
        if (!$ticketData) {
            return ApiHelper::response(false, __('messages.ticket.not_found'), null, 404);
        }

        try {
            $insertData = $request->all();
            $insertData['owner_id'] = Auth::id();
            $insertData['status'] = -1;
            $insertData['created_by'] = Auth::id();

            DB::beginTransaction();
            $ticketData->testCases()->create($insertData);
            DB::commit();

            $data = $ticketData->testCases()->get();
            return ApiHelper::response(true, __('messages.test_case.store_success'), $data, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiHelper::response(false, __('messages.test_case.store_error'), null, 500);
        }
    }

    /**
     * Update an existing TestCase for the given ticket.
     *
     * @param int $ticket_id
     * @param int $test_case_id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(int $ticket_id, int $test_case_id, Request $request): JsonResponse
    {
        // Check if the ticket exists
        $ticketData = Ticket::find($ticket_id);
        if (!$ticketData) {
            return ApiHelper::response(false, __('messages.ticket.not_found'), null, 404);
        }

        try {
            $updateData = $request->except(['ticket_id', 'test_case_id']);
            $updateData['updated_by'] = Auth::id();

            DB::beginTransaction();
            $updated = $ticketData->testCases()->where('id', $test_case_id)->update($updateData);
            DB::commit();

            if ($updated) {
                $data = $ticketData->testCases()->get();
                return ApiHelper::response(true, __('messages.test_case.update_success'), $data, 200);
            }

            return ApiHelper::response(false, __('messages.test_case.update_error'), null, 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiHelper::response(false, __('messages.test_case.update_error'), null, 500);
        }
    }
}

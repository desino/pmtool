<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTestCaseRequest;
use App\Models\Initiative;
use App\Models\Ticket;
use App\Models\TicketAction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestCaseController extends Controller
{
    /**
     * Store a new TestCase for the given ticket.
     *
     * @param StoreTestCaseRequest $request
     * @param int $initiative_id
     * @param int $ticket_id
     * @return JsonResponse
     */
    public function store(StoreTestCaseRequest $request, int $initiative_id, int $ticket_id): JsonResponse
    {
        // Fetch ticket and initiative data with validation checks
        $ticketData = Ticket::with('currentAction')->find($ticket_id);
        $initiativeData = Initiative::find($initiative_id);

        if (!$ticketData || !$initiativeData) {
            return ApiHelper::response(false, __('messages.ticket.not_found'), null, 404);
        }

        // Check ownership and permissions
        if (!$this->isAuthorizedToCreateTestCase($initiativeData, $ticketData)) {
            return ApiHelper::response(false, __('messages.ticket.create_testcase_not_allowed'), '', 400);
        }

        try {
            $insertData = array_merge(
                $request->validated(),
                ['owner_id' => Auth::id(), 'status' => -1, 'created_by' => Auth::id()]
            );

            DB::beginTransaction();
            $ticketData->testCases()->create($insertData);
            DB::commit();

            return ApiHelper::response(true, __('messages.test_case.store_success'), $ticketData->testCases()->get(), 200);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiHelper::response(false, __('messages.test_case.store_error'), null, 500);
        }
    }

    /**
     * Show a specific TestCase for the given ticket.
     *
     * @param int $initiative_id
     * @param int $ticket_id
     * @param int $test_case_id
     * @return JsonResponse
     */
    public function show(int $initiative_id, int $ticket_id, int $test_case_id): JsonResponse
    {
        $ticketData = Ticket::with('currentAction')->find($ticket_id);
        $initiativeData = Initiative::find($initiative_id);

        if (!$ticketData) {
            return ApiHelper::response(false, __('messages.ticket.not_found'), null, 404);
        }

        if (!$initiativeData) {
            return ApiHelper::response(false, __('messages.initiative.not_found'), null, 404);
        }

        $testCase = $ticketData->testCases()->find($test_case_id);
        if (!$testCase) {
            return ApiHelper::response(false, __('messages.test_case.not_found'), null, 404);
        }

        return ApiHelper::response(true, __('messages.test_case.get_success'), $testCase, 200);
    }

    /**
     * Update an existing TestCase for the given ticket.
     *
     * @param int $ticket_id
     * @param int $test_case_id
     * @param StoreTestCaseRequest $request
     * @return JsonResponse
     */
    public function update(int $initiative_id,int $ticket_id, int $test_case_id, StoreTestCaseRequest $request): JsonResponse
    {
        $ticketData = Ticket::find($ticket_id);

        if (!$ticketData) {
            return ApiHelper::response(false, __('messages.ticket.not_found'), null, 404);
        }

        $initiativeData = Initiative::find($initiative_id);
        if (!$initiativeData) {
            return ApiHelper::response(false, __('messages.initiative.not_found'), null, 404);
        }

        if (!$this->isAuthorizedToProcessTestCase($initiativeData, $ticketData)) {
            return ApiHelper::response(false, __('messages.ticket.process_testcase_not_allowed'), '', 400);
        }

        try {
            $updateData = array_merge(
                $request->validated(),
                ['updated_by' => Auth::id()]
            );

            if (isset($updateData['comment'])) {
                $updateData['status'] = 0;
            }

            DB::beginTransaction();
            $updated = $ticketData->testCases()->where('id', $test_case_id)->update($updateData);
            DB::commit();

            if ($updated) {
                return ApiHelper::response(true, __('messages.test_case.update_success'), $ticketData->testCases()->get(), 200);
            }

            return ApiHelper::response(false, __('messages.test_case.update_error'), null, 400);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiHelper::response(false, __('messages.test_case.update_error'), null, 500);
        }
    }

    /**
     * Check if the user is authorized to create a TestCase.
     *
     * @param Initiative $initiativeData
     * @param Ticket $ticketData
     * @return bool
     */
    private function isAuthorizedToCreateTestCase(Initiative $initiativeData, Ticket $ticketData): bool
    {
        $isFunctionalOwner = Auth::id() === $initiativeData->functional_owner_id;
        $isTechnicalOwner = Auth::id() === $initiativeData->technical_owner_id;
        $isQualityOwner = Auth::id() === $initiativeData->quality_owner_id;

        $isTestAction = $ticketData->currentAction->action === TicketAction::getActionTest();
        $isActionable = $ticketData->currentAction->status === TicketAction::getStatusActionable();

        if ($isFunctionalOwner || $isTechnicalOwner) {
            return true;
        }

        return $isQualityOwner && $isTestAction && $isActionable;
    }

    /**
     * Check if the user is authorized to process a TestCase.
     *
     * @param Initiative $initiativeData
     * @param Ticket $ticketData
     * @return bool
     */
    private function isAuthorizedToProcessTestCase(Initiative $initiativeData, Ticket $ticketData): bool
    {
        $isQualityOwner = Auth::id() === $initiativeData->quality_owner_id;
        $isTestAction = $ticketData->currentAction->action === TicketAction::getActionTest();
        $isActionable = $ticketData->currentAction->status === TicketAction::getStatusActionable();

        return $isQualityOwner || ($isTestAction && $isActionable);
    }
}

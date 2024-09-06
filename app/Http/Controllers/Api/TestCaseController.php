<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\TestCase;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestCaseController extends Controller
{
    public function store(Request $request,$ticket_id): JsonResponse
    {
        $insertData = $request->all();
        $insertData['owner_id'] = \Auth::id();
        $insertData['status'] = -1;
        $insertData['created_by'] = \Auth::id();
        $data =Ticket::find($ticket_id)->testCases()->create($insertData);
        if($data)
        {
            return ApiHelper::response(true, __('messages.test_case.store_success'), $data, 200);
        }
        return ApiHelper::response(false, __('messages.test_case.store_error'), $data, 400);
    }

    public function update($ticket_id,$test_case_id,Request $request): JsonResponse
    {
        $data = Ticket::find($ticket_id)->testCases();
        $updateData = $request->all();
        unset($updateData['ticket_id']);
        unset($updateData['test_case_id']);
        $updateData['updated_by'] = \Auth::id();
        $data->where('id', $test_case_id)->update($updateData);
        if($data)
        {
            return ApiHelper::response(true, __('messages.test_case.update_success'), $data, 200);
        }
        return ApiHelper::response(false, __('messages.test_case.update_error'), $data, 400);
    }
}

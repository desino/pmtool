<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestApiController extends Controller
{
    const DEFAULT_TABLES = [
        'clients',
        'functionalities',
        'initiatives',
        'initiative_environments',
        'plannings',
        'planning_assignments',
        'projects',
        'releases',
        'release_tickets',
        'sections',
        'test_cases',
        'tickets',
        'ticket_actions',
        'time_bookings',
        'users',
    ];
    public function databaseTableData(Request $request)
    {
        $requestData = $request->all();
        $tableName = self::DEFAULT_TABLES;
        if (isset($requestData['table_name']) && $requestData['table_name'] != '') {
            $tableName = [$requestData['table_name']];
        }
        $retData = [];
        foreach ($tableName as $table) {
            if (Schema::hasTable($table)) {
                $retData[$table] = DB::table($table)->get();
            }
        }
        return response([
            'message' => '',
            'data' => $retData
        ], 200);
        return ApiHelper::response(true, '', $retData, 200);
    }
}

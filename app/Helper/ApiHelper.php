<?php

namespace App\Helper;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiHelper
{
    /**
     * Generate a JSON response for an API endpoint.
     *
     * @param bool $status The status of the response. Defaults to an empty string.
     * @param string $message The message of the response. Defaults to an empty string.
     * @param mixed $content The data to be included in the response. Defaults to an empty string.
     * @param int $http_code The HTTP status code of the response. Defaults to 500.
     * @return JsonResponse The JSON response.
     */
    public static function response(bool $status = false, string $message = 'Something Went Wrong', mixed $content = '', int $http_code = 500): JsonResponse
    {

        $response['status'] = $status;
        $response['message'] = $message;
        /*if ($content instanceof LengthAwarePaginator) {
            $paginate = [
                'total' => $content->total(),
                'per_page' => $content->perPage(),
                'current_page' => $content->currentPage(),
                'last_page' => $content->lastPage(),
            ];
            $response['paginate'] = $paginate;
            $content = $content->items();
        }*/
        $response['content'] = $content;

        return response()->json($response, $http_code);
    }

    public static function parsePagination($paginatedResult)
    {
        $parsedPagination = [
            'records' => null,
            'paginationInfo' => [
                'total' => null,
                'per_page' => null,
                'current_page' => null,
                'last_page' => null,
            ],
        ];

        if ($paginatedResult instanceof LengthAwarePaginator) {
            $parsedPagination['records'] = $paginatedResult->items();
            $parsedPagination['paginationInfo']['total'] = $paginatedResult->total();
            $parsedPagination['paginationInfo']['per_page'] = $paginatedResult->perPage();
            $parsedPagination['paginationInfo']['current_page'] = $paginatedResult->currentPage();
            $parsedPagination['paginationInfo']['last_page'] = $paginatedResult->lastPage();
        };

        return $parsedPagination;
    }
}
 
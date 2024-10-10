<?php

namespace App\Services;

use App\Models\Initiative;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlanningService
{
    public static function getInitiatives()
    {
        return Initiative::select('id', 'name', 'client_id')
            ->with([
                'client' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->orderBy('id', 'desc')->get();
    }

    public static function getUsers()
    {
        $user = User::select('id', 'name')->get();
        return $user;
    }
}

<?php

namespace App\Services;

use App\Models\Release;

class ReleaseService
{

    public static function getUnprocessedRelease($initiativeId)
    {
        $releases = Release::where('initiative_id', $initiativeId)->where('status', Release::UNPROCESSED_RELEASE)->get();
        return $releases;
    }
}

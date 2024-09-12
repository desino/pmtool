<?php

namespace App\Services;

use App\Models\Release;

class ReleaseService
{

    public static function getUnprocessedRelease()
    {
        $releases = Release::where('status', Release::UNPROCESSED_RELEASE)->get();
        return $releases;
    }
}

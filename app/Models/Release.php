<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'status_name'
    ];

    public const UNPROCESSED_RELEASE = 0;
    public const PROCESSED_RELEASE = 1;

    public function getAllStatus()
    {
        return [
            ['id' => self::UNPROCESSED_RELEASE, 'name' => __('release_status.unprocessed')],
            ['id' => self::PROCESSED_RELEASE, 'name' => __('release_status.processed')],
        ];
    }

    public static function getStatus($id)
    {
        return match ($id) {
            self::UNPROCESSED_RELEASE => __('release_status.unprocessed'),
            self::PROCESSED_RELEASE => __('release_status.processed'),
            default => '-'
        };
    }

    public function getStatusNameAttribute()
    {
        return match ($this->action) {
            self::UNPROCESSED_RELEASE => __('release_status.unprocessed'),
            self::PROCESSED_RELEASE => __('release_status.processed'),
            default => '-'
        };
    }

    public function releaseTickets()
    {
        return $this->hasMany(ReleaseTicket::class);
    }
}

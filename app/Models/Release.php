<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    protected $appends = [
        'status_name',
        'request_date_format',
        'deployment_date_format',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public const UNPROCESSED_RELEASE = 0;
    public const PROCESSED_RELEASE = 1;

    public function tickets()
    {
        return $this->hasMany(ReleaseTicket::class);
    }

    public function initiative()
    {
        return $this->belongsTo(Initiative::class);
    }

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
        return match ($this->status) {
            self::UNPROCESSED_RELEASE => __('release_status.unprocessed'),
            self::PROCESSED_RELEASE => __('release_status.processed'),
            default => '-'
        };
    }

    public function getRequestDateFormatAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y') : '';
    }

    public function getDeploymentDateFormatAttribute()
    {
        return $this->processed_at ? $this->processed_at->format('d/m/Y') : '';
    }

    public static function getAllReleases($initiativeId)
    {
        $releases = self::where('initiative_id', $initiativeId)->get();
        return $releases;
    }
}

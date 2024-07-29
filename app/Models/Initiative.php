<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Initiative extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public const STATUS_OPPORTUNITY = 1;
    public const STATUS_ONGOING = 2;
    public const STATUS_CLOSED = 3;
    public const STATUS_LOST = 4;

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public static function statuses()
    {
        return [            
            self::STATUS_OPPORTUNITY => __('initiative.status.opportunity'),
            self::STATUS_ONGOING => __('initiative.status.ongoing'),
            self::STATUS_CLOSED => __('initiative.status.closed'),
            self::STATUS_LOST => __('initiative.status.lost'),
        ];
    }

    public static function getOpportunity()
    {
        return self::STATUS_OPPORTUNITY;        
    }

    public static function getOngoing()
    {
        return self::STATUS_ONGOING;        
    }

    public static function getClosed()
    {
        return self::STATUS_CLOSED;        
    }

    public static function getLost()
    {
        return self::STATUS_LOST;        
    }
}

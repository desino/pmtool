<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'type_label'
    ];

    PUBLIC CONST TYPE_FEATURE_IMPROVEMENT = 1;
    PUBLIC CONST TYPE_FEATURE_BUG = 2;
    PUBLIC CONST TYPE_FEATURE_DEVELOPMENT = 3;
    PUBLIC CONST TYPE_FEATURE_MAINTAINANCE_TASK = 4;

    public static function getAllTypes()
    {
        return [
            self::TYPE_FEATURE_IMPROVEMENT => __('ticket_type.feature.improvement'),
            self::TYPE_FEATURE_BUG => __('ticket_type.feature.bug'),
            self::TYPE_FEATURE_DEVELOPMENT => __('ticket_type.feature.development'),
            self::TYPE_FEATURE_MAINTAINANCE_TASK => __('ticket_type.feature.maintainance_task'),
        ];
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            self::TYPE_FEATURE_IMPROVEMENT => __('ticket_type.feature.improvement'),
            self::TYPE_FEATURE_BUG => __('ticket_type.feature.bug'),
            self::TYPE_FEATURE_DEVELOPMENT => __('ticket_type.feature.development'),
            self::TYPE_FEATURE_MAINTAINANCE_TASK => __('ticket_type.feature.maintainance_task'),
            default => '-'
        };
    }

    public static function getTypeFeatureImprovement()
    {
        return self::TYPE_FEATURE_IMPROVEMENT;
    }
    public static function getTypeFeatureBug()
    {
        return self::TYPE_FEATURE_BUG;
    }
    public static function getTypeFeatureDevelopment()
    {
        return self::TYPE_FEATURE_DEVELOPMENT;
    }
    public static function getTypeFeatureMaintainanceTask()
    {
        return self::TYPE_FEATURE_MAINTAINANCE_TASK;
    }

    public function functionality(): BelongsTo
    {
        return $this->belongsTo(Functionality::class);
    }
}

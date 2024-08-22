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

    public const TYPE_FEATURE_IMPROVEMENT = 1;
    public const TYPE_FEATURE_BUG = 2;
    public const TYPE_FEATURE_DEVELOPMENT = 3;
    public const TYPE_FEATURE_MAINTENANCE_TASK = 4;

    public static function getAllTypes()
    {
        return [
            self::TYPE_FEATURE_IMPROVEMENT => __('ticket_type.feature.improvement'),
            self::TYPE_FEATURE_BUG => __('ticket_type.feature.bug'),
            self::TYPE_FEATURE_DEVELOPMENT => __('ticket_type.feature.development'),
            self::TYPE_FEATURE_MAINTENANCE_TASK => __('ticket_type.feature.maintenance_task'),
        ];
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            self::TYPE_FEATURE_IMPROVEMENT => __('ticket_type.feature.improvement'),
            self::TYPE_FEATURE_BUG => __('ticket_type.feature.bug'),
            self::TYPE_FEATURE_DEVELOPMENT => __('ticket_type.feature.development'),
            self::TYPE_FEATURE_MAINTENANCE_TASK => __('ticket_type.feature.maintenance_task'),
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
    public static function getTypeFeatureMaintenanceTask()
    {
        return self::TYPE_FEATURE_MAINTENANCE_TASK;
    }

    public function functionality(): BelongsTo
    {
        return $this->belongsTo(Functionality::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}

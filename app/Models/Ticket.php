<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'type_label',
        'display_created_at',
        'asana_task_link'
    ];

    public const TYPE_CHANGE_REQUEST = 1;
    public const TYPE_BUG = 2;
    public const TYPE_DEVELOPMENT = 3;
    public const TYPE_MAINTENANCE = 4;

    public static function getAllTypes()
    {
        return [
            ['id' => self::TYPE_CHANGE_REQUEST, 'name' => __('ticket_type.change_request')],
            ['id' => self::TYPE_BUG, 'name' => __('ticket_type.bug')],
            ['id' => self::TYPE_DEVELOPMENT, 'name' => __('ticket_type.development')],
            ['id' => self::TYPE_MAINTENANCE, 'name' => __('ticket_type.maintenance')],
        ];
    }

    public static function getTypeOfCode($typeId)
    {
        return match ($typeId) {
            self::TYPE_CHANGE_REQUEST => __('ticket_type.change_request.code'),
            self::TYPE_BUG => __('ticket_type.bug.code'),
            self::TYPE_DEVELOPMENT => __('ticket_type.development.code'),
            self::TYPE_MAINTENANCE => __('ticket_type.maintenance.code'),
            default => '-'
        };
    }

    public static function getTypeChangeRequest()
    {
        return self::TYPE_CHANGE_REQUEST;
    }
    public static function getTypeBug()
    {
        return self::TYPE_BUG;
    }
    public static function getTypeDevelopment()
    {
        return self::TYPE_DEVELOPMENT;
    }
    public static function getTypeMaintenanceTask()
    {
        return self::TYPE_MAINTENANCE;
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            self::TYPE_CHANGE_REQUEST => __('ticket_type.change_request'),
            self::TYPE_BUG => __('ticket_type.bug'),
            self::TYPE_DEVELOPMENT => __('ticket_type.development'),
            self::TYPE_MAINTENANCE => __('ticket_type.maintenance'),
            default => '-'
        };
    }

    protected function displayCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at ? $this->created_at->format('m/d/Y') : '',
        );
    }

    protected function asanaTaskLink(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->initiative_id ? "https://app.asana.com/0/" . $this->initiative->asana_project_id . '/' . $this->asana_task_id . '/f' : null,
        );
    }

    public function functionality(): BelongsTo
    {
        return $this->belongsTo(Functionality::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function initiative()
    {
        return $this->belongsTo(Initiative::class);
    }
}

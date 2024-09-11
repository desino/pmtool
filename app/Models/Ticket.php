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
        'status_label',
        'display_created_at',
        'asana_task_link'
    ];

    public const TYPE_CHANGE_REQUEST = 1;
    public const TYPE_BUG = 2;
    public const TYPE_DEVELOPMENT = 3;
    public const TYPE_MAINTENANCE = 4;

    public const STATUS_ONGOING = 1;
    public const STATUS_WAIT_FOR_CLIENT = 2;
    public const STATUS_READY_FOR_TEST = 3;
    public const STATUS_READY_FOR_ACC = 4;
    public const STATUS_READY_FOR_PRD = 5;
    public const STATUS_DONE = 6;

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

    public static function getAllStatus()
    {
        return [
            ['id' => self::STATUS_ONGOING, 'name' => __('ticket_status.ongoing')],
            ['id' => self::STATUS_WAIT_FOR_CLIENT, 'name' => __('ticket_status.wait_for_client')],
            ['id' => self::STATUS_READY_FOR_TEST, 'name' => __('ticket_status.ready_for_test')],
            ['id' => self::STATUS_READY_FOR_ACC, 'name' => __('ticket_status.ready_for_acc')],
            ['id' => self::STATUS_READY_FOR_PRD, 'name' => __('ticket_status.ready_for_prd')],
            ['id' => self::STATUS_DONE, 'name' => __('ticket_status.done')],
        ];
    }

    public static function getStatusOngoing()
    {
        return self::STATUS_ONGOING;
    }
    public static function getStatusWaitForClient()
    {
        return self::STATUS_WAIT_FOR_CLIENT;
    }
    public static function getStatusReadyForTest()
    {
        return self::STATUS_READY_FOR_TEST;
    }
    public static function getStatusReadyForACC()
    {
        return self::STATUS_READY_FOR_ACC;
    }
    public static function getStatusReadyForPRD()
    {
        return self::STATUS_READY_FOR_PRD;
    }
    public static function getStatusDone()
    {
        return self::STATUS_DONE;
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
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_ONGOING => __('ticket_status.ongoing'),
            self::STATUS_WAIT_FOR_CLIENT => __('ticket_status.wait_for_client'),
            self::STATUS_READY_FOR_TEST => __('ticket_status.ready_for_test'),
            self::STATUS_READY_FOR_ACC => __('ticket_status.ready_for_acc'),
            self::STATUS_READY_FOR_PRD => __('ticket_status.ready_for_prd'),
            self::STATUS_DONE => __('ticket_status.done'),
            default => '-'
        };
    }

    protected function displayCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at ? $this->created_at->format('m/d/Y') : '',
        );
    }

    public function asanaTaskLink(): Attribute
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

    public function actions()
    {
        return $this->hasMany(TicketAction::class)->orderBy('action');
    }

    public function testCases()
    {
        return $this->hasMany(TestCase::class);
    }

    public function doneActions()
    {
        return $this->hasOne(TicketAction::class)->where('status', TicketAction::getStatusDone())->orderBy('action');
    }

    public function currentAction()
    {
        return $this->hasOne(TicketAction::class)
            ->with('user')
            ->where('status', '!=', TicketAction::getStatusDone())
            ->orderBy('action')->latest();
    }

    public function nextAction()
    {
        return $this->hasOne(TicketAction::class)
            ->with('user')
            ->where('status', '!=', TicketAction::getStatusDone())
            ->orderBy('action')->skip(1)->take(1);
    }

    public function previousAction()
    {
        return $this->hasOne(TicketAction::class)
            ->with('user')
            ->where('status', '>', TicketAction::getStatusActionable())
            ->orderBy('action', 'desc')->latest();
    }

    public function scopeReadyForTestStatus($query)
    {
        return $query->where('status', Self::getStatusReadyForTest());
    }
}

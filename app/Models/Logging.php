<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logging extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'display_activity_type',
        'display_activity_detail',
        'display_created_at',
    ];

    public const ACTIVITY_TYPE_MARKED_AS_DONE = 1;
    public const ACTIVITY_TYPE_MOVED_BACK_TO = 2;
    public const ACTIVITY_TYPE_DEPLOYMENT = 3;
    public const ACTIVITY_TYPE_DELETE = 4;

    public const ACTIVITY_DETAIL_DETAIL = 1;
    public const ACTIVITY_DETAIL_CLARIFY_ESTIMATE = 2;
    public const ACTIVITY_DETAIL_DEVELOP = 3;
    public const ACTIVITY_DETAIL_TEST = 4;
    public const ACTIVITY_DETAIL_VALIDATE = 5;
    public const ACTIVITY_DETAIL_DONE = 6;
    public const ACTIVITY_DETAIL_ACC = 7;
    public const ACTIVITY_DETAIL_DELETED = 8;

    public static function getAllActivityTypes()
    {
        return [
            ['id' => self::ACTIVITY_TYPE_MARKED_AS_DONE, 'name' => __('logging_activity_type.marked_as_done')],
            ['id' => self::ACTIVITY_TYPE_MOVED_BACK_TO, 'name' => __('logging_activity_type.moved_back_to')],
            ['id' => self::ACTIVITY_TYPE_DEPLOYMENT, 'name' => __('logging_activity_type.deployment')],
            ['id' => self::ACTIVITY_TYPE_DELETE, 'name' => __('logging_activity_type.delete')],
        ];
    }

    public static function getAllActivityDetails()
    {
        return [
            ['id' => self::ACTIVITY_DETAIL_DETAIL, 'name' => __('logging_activity_detail.detail'), 'color' => 'color-macro-status-detail-ticket'],
            ['id' => self::ACTIVITY_DETAIL_CLARIFY_ESTIMATE, 'name' => __('logging_activity_detail.clarify_estimate'), 'color' => 'color-macro-status-clarify-and-estimate'],
            ['id' => self::ACTIVITY_DETAIL_DEVELOP, 'name' => __('logging_activity_detail.develop'), 'color' => 'color-macro-status-develop'],
            ['id' => self::ACTIVITY_DETAIL_TEST, 'name' => __('logging_activity_detail.test'), 'color' => 'color-macro-status-test'],
            ['id' => self::ACTIVITY_DETAIL_VALIDATE, 'name' => __('logging_activity_detail.validate'), 'color' => 'color-macro-status-validate'],
            ['id' => self::ACTIVITY_DETAIL_DONE, 'name' => __('logging_activity_detail.done'), 'color' => 'color-macro-status-done'],
            ['id' => self::ACTIVITY_DETAIL_ACC, 'name' => __('logging_activity_detail.acc'), 'color' => 'color-activity-log-deploy-acc'],
            ['id' => self::ACTIVITY_DETAIL_DELETED, 'name' => __('logging_activity_detail.deleted'), 'color' => 'color-activity-log-deleted'],
        ];
    }

    public function getDisplayActivityTypeAttribute()
    {
        return self::getAllActivityTypes()[$this->activity_type - 1] ?? '-';
    }

    public function getDisplayActivityDetailAttribute()
    {
        return self::getAllActivityDetails()[$this->activity_detail - 1] ?? '-';
    }
    public function getDisplayCreatedAtAttribute()
    {
        return  $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : '';
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

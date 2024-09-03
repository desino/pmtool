<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'action_name',
        'action_status'
    ];

    protected const ACTION_DETAIL_TICKET = 1;
    protected const ACTION_CLARIFY_AND_ESTIMATE = 2;
    protected const ACTION_DEVELOP = 3;
    protected const ACTION_TEST = 4;
    protected const ACTION_VALIDATE = 5;

    protected const ACTION_STATUS_ACTIONABLE = 1;
    protected const ACTION_STATUS_WAITING_FOR_DEPENDANT_ACTION = 0;
    protected const ACTION_STATUS_DONE = 2;

    public static function getAllActions()
    {
        return [
            ['id' => self::ACTION_DETAIL_TICKET, 'name' => __('ticket_action.detail_ticket')],
            ['id' => self::ACTION_CLARIFY_AND_ESTIMATE, 'name' => __('ticket_action.clarify_and_estimate')],
            ['id' => self::ACTION_DEVELOP, 'name' => __('ticket_action.develop')],
            ['id' => self::ACTION_TEST, 'name' => __('ticket_action.test')],
            ['id' => self::ACTION_VALIDATE, 'name' => __('ticket_action.validate')],
        ];
    }

    public static function getAllActionStatus()
    {
        return [
            ['id' => self::ACTION_STATUS_WAITING_FOR_DEPENDANT_ACTION, 'name' => __('ticket_action_status.waiting_for_dependant_action')],
            ['id' => self::ACTION_STATUS_ACTIONABLE, 'name' => __('ticket_action_status.actionable')],
            ['id' => self::ACTION_STATUS_DONE, 'name' => __('ticket_action_status.done')],
        ];
    }

    public static function getStatusActionable()
    {
        return self::ACTION_STATUS_ACTIONABLE;
    }

    public static function getStatusWaitingForDependantAction()
    {
        return self::ACTION_STATUS_WAITING_FOR_DEPENDANT_ACTION;
    }

    public static function getStatusDone()
    {
        return self::ACTION_STATUS_DONE;
    }

    public static function getActionDetailTicket()
    {
        return self::ACTION_DETAIL_TICKET;
    }
    public static function getActionClarifyAndEstimate()
    {
        return self::ACTION_CLARIFY_AND_ESTIMATE;
    }
    public static function getActionDevelop()
    {
        return self::ACTION_DEVELOP;
    }
    public static function getActionTest()
    {
        return self::ACTION_TEST;
    }
    public static function getActionValidate()
    {
        return self::ACTION_VALIDATE;
    }

    public static function getActionStatusNameById($id)
    {
        return match ($id) {
            self::ACTION_STATUS_WAITING_FOR_DEPENDANT_ACTION => __('ticket_action_status.waiting_for_dependant_action'),
            self::ACTION_STATUS_ACTIONABLE => __('ticket_action_status.actionable'),
            self::ACTION_STATUS_DONE => __('ticket_action_status.done'),
            default => '-'
        };
    }

    public function getActionNameAttribute()
    {
        return match ($this->action) {
            self::ACTION_DETAIL_TICKET => __('ticket_action.detail_ticket'),
            self::ACTION_CLARIFY_AND_ESTIMATE => __('ticket_action.clarify_and_estimate'),
            self::ACTION_DEVELOP => __('ticket_action.develop'),
            self::ACTION_TEST => __('ticket_action.test'),
            self::ACTION_VALIDATE => __('ticket_action.validate'),
            default => '-'
        };
    }

    public function getActionStatusAttribute()
    {
        return match ($this->status) {
            self::ACTION_STATUS_WAITING_FOR_DEPENDANT_ACTION => __('ticket_action_status.waiting_for_dependant_action'),
            self::ACTION_STATUS_ACTIONABLE => __('ticket_action_status.actionable'),
            self::ACTION_STATUS_DONE => __('ticket_action_status.done'),
            default => '-'
        };
    }
}

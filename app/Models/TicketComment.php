<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected const COMMENT_TYPE_NORMAL = 1;
    protected const COMMENT_TYPE_PREVIOUS_ACTION = 2;

    public static function getAllCommentTypes()
    {
        return [
            ['id' => self::COMMENT_TYPE_NORMAL, 'name' => __('comment.comment_type.normal')],
            ['id' => self::COMMENT_TYPE_PREVIOUS_ACTION, 'name' => __('comment.comment_type.previous_action')],
        ];
    }
}

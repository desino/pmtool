<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'display_created_at',
        'display_updated_at',
    ];

    protected const COMMENT_TYPE_NORMAL = 1;
    protected const COMMENT_TYPE_PREVIOUS_ACTION = 2;

    public static function getAllCommentTypes()
    {
        return [
            ['id' => self::COMMENT_TYPE_NORMAL, 'name' => __('comment.comment_type.normal')],
            ['id' => self::COMMENT_TYPE_PREVIOUS_ACTION, 'name' => __('comment.comment_type.previous_action')],
        ];
    }

    protected function displayCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at ? $this->created_at->format('d/m/Y H:i:s A') : '',
        );
    }
    protected function displayUpdatedAt(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s A') : null,
        );
    }
}

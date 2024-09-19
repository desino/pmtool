<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitiativeEnvironment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'server_type_label',
    ];

    public const SERVER_TYPE_TEST = 1;
    public const SERVER_TYPE_ACC = 2;
    public const SERVER_TYPE_PRD = 3;

    public function initiative()
    {
        return $this->belongsTo(Initiative::class);
    }

    public static function getServerTypeAll()
    {
        return [
            ['id' => self::SERVER_TYPE_TEST, 'name' => __('initiative_environment_server_type_test')],
            ['id' => self::SERVER_TYPE_ACC, 'name' => __('initiative_environment_server_type_acc')],
            ['id' => self::SERVER_TYPE_PRD, 'name' => __('initiative_environment_server_type_prd')],
        ];
    }

    public function getServerTypeLabelAttribute()
    {
        return match ($this->status) {
            self::SERVER_TYPE_TEST => __('initiative_environment_server_type_test'),
            self::SERVER_TYPE_ACC => __('initiative_environment_server_type_acc'),
            self::SERVER_TYPE_PRD => __('initiative_environment_server_type_prd'),
            default => '-'
        };
    }
}

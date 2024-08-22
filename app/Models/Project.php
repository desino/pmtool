<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public const DEFAULT_PROJECT_NAME = 'Maintenance';

    public static function getDefaultProjectName()
    {
        return self::DEFAULT_PROJECT_NAME;
    }
}

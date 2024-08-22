<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Project extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function getDefaultProjectName()
    {
        return Config::get('myapp.default_project_name');
    }
}

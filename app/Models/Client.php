<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function initiatives()
    {
        return $this->hasMany(Initiative::class);
    }
    public function initiative()
    {
        return $this->hasOne(Initiative::class);
    }
}
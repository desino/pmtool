<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitiativeEnvironment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function initiative()
    {
        return $this->belongsTo(Initiative::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeBooking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function initiative()
    {
        return $this->belongsTo(Initiative::class);
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class);
    }
}

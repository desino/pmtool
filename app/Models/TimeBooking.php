<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeBooking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'show_booked_date'
    ];

    public function initiative()
    {
        return $this->belongsTo(Initiative::class);
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getShowBookedDateAttribute()
    {
        return $this->booked_date != '' ? Carbon::parse($this->booked_date)->format('d/m/Y') : '';
    }
}

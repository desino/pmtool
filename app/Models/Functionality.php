<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Functionality extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function openTickets()
    {
        return $this->hasMany(Ticket::class)->where('status', '!=', Ticket::getStatusDone());
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

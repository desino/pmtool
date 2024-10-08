<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReleaseTicket extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function release()
    {
        return $this->belongsTo(Release::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}

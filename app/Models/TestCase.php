<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCase extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'status_label',
    ];

    public function getStatusLabelAttribute()
    {
        return $this->status == -1 ? 'Pending' : ($this->status == 0 ? 'Failed' : 'Passed');
    }
}

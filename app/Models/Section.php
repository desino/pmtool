<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeInitiativeId($query, int|array $value)
    {
        if (is_array($value)) {
            return $query->whereIn('initiative_id', $value);
        }
        return $query->where('initiative_id', $value);
    }

    public function functionalities()
    {
        return $this->hasMany(Functionality::class)->orderBy('order_no', 'asc');
    }

    public function initiative()
    {
        return $this->belongsTo(Initiative::class);
    }
}

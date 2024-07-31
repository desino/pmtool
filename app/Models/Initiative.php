<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Initiative extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $append = ['client_initiative_name','simple_attribute'];

    public const STATUS_OPPORTUNITY = 1;
    public const STATUS_ONGOING = 2;
    public const STATUS_CLOSED = 3;
    public const STATUS_LOST = 4;

    protected function clientInitiativeName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->client->name." - ".$this->name,
        );
    }    

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public static function statuses()
    {
        return [            
            self::STATUS_OPPORTUNITY => __('initiative.status.opportunity'),
            self::STATUS_ONGOING => __('initiative.status.ongoing'),
            self::STATUS_CLOSED => __('initiative.status.closed'),
            self::STATUS_LOST => __('initiative.status.lost'),
        ];
    }

    public static function getStatusOpportunity()
    {
        return self::STATUS_OPPORTUNITY;        
    }

    public static function getStatusOngoing()
    {
        return self::STATUS_ONGOING;        
    }

    public static function getStatusClosed()
    {
        return self::STATUS_CLOSED;        
    }

    public static function getStatusLost()
    {
        return self::STATUS_LOST;        
    }

    public function scopeStatus($query, int|array $status)
    {
        if (is_array($status)) {
            return $query->whereIn('status', $status);
        }
        return $query->where('status', $status);
    }
    public function scopeName($query, string $value)
    {        
        return $query->where('name', 'like', '%'.$value.'%');
    }

    // change with scope and ass in servoce
    // public static function getInitiatives($status = null){
    //     $self = self::select('*')
    //     ->when($status != null, function($query) use ($status){
    //         return $query->where('status', $status);
    //     })
    //     ->get();
    //     return $self;
    // }
}

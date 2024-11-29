<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Initiative extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $appends = ['client_initiative_name', 'creation_date'];

    public const STATUS_OPPORTUNITY = 1;
    public const STATUS_ONGOING = 2;
    public const STATUS_CLOSED = 3;
    public const STATUS_LOST = 4;

    protected function clientInitiativeName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->client?->name . " - " . $this->name,
        );
    }
    protected function creationDate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at ? $this->created_at->format('d/m/Y') : '',
        );
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function initiativeEnvironments()
    {
        return $this->hasMany(InitiativeEnvironment::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function unprocessedRelease()
    {
        return $this->hasOne(Release::class)->where('status', Release::UNPROCESSED_RELEASE);
    }

    public function releases()
    {
        return $this->hasMany(Release::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
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

    public function functionalOwner()
    {
        return $this->belongsTo(User::class, 'functional_owner_id');
    }

    public function qualityOwner()
    {
        return $this->belongsTo(User::class, 'quality_owner_id');
    }

    public function technicalOwner()
    {
        return $this->belongsTo(User::class, 'technical_owner_id');
    }

    public function timeBookings()
    {
        return $this->hasMany(TimeBooking::class);
    }

    public function timeBookingsWithoutTickets()
    {
        return $this->hasMany(TimeBooking::class)->whereNull('ticket_id');
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
        return $query->where('name', 'like', '%' . $value . '%');
    }

    public function scopeClient($query, int|array $value)
    {
        if (is_array($value)) {
            return $query->whereIn('client_id', $value);
        }
        return $query->where('client_id', $value);
    }
}

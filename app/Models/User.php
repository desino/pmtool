<?php

namespace App\Models;

use App\Mail\ResetPasswordMail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['profile_photo_url'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        try {
            Mail::to($this->email)->send(new ResetPasswordMail($token, $this->email));
        } catch (\Exception $e) {
            logger()->error($e);
            logger()->error($e);
        }
        //$this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the 
     *
     * @param  string  $value
     * @return string
     */
    public function getProfilePhotoUrlAttribute($value)
    {
        return File::exists(public_path('images/profile_photos/'.$this->email.'.png')) ? asset('images/profile_photos/'.$this->email.'.png') : asset('images/profile_photos/default.png');
    }
}

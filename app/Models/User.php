<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * Match database defaults on new models before they are reloaded.
     */
    protected $attributes = [
        'role' => 'user',
        'status' => 'active',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => 'string',
        'status' => 'string',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'role_display',
    ];

    /**
     * Method untuk cek apakah user admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Method untuk cek apakah user biasa
     */
    public function isUser()
    {
        return $this->role === 'user';
    }

    /**
     * Method untuk mendapatkan display name role
     */
    public function getRoleDisplayAttribute()
    {
        return [
            'user' => 'User',
            'admin' => 'Administrator',
        ][$this->role] ?? 'User';
    }

    /**
     * Method untuk cek apakah user active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Method untuk cek apakah user inactive
     */
    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    /**
     * Method untuk cek apakah user disabled
     */
    public function isDisabled()
    {
        return $this->status === 'disabled';
    }

    /**
     * Method untuk cek apakah user bisa login (active or inactive)
     */
    public function canLogin()
    {
        return in_array($this->status, ['active', 'inactive']);
    }

    /**
     * Method untuk mendapatkan display name status
     */
    public function getStatusDisplayAttribute()
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'disabled' => 'Disabled',
        ][$this->status] ?? 'Active';
    }

    /**
     * Method untuk mendapatkan status dengan badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return [
            'active' => 'success',
            'inactive' => 'warning',
            'disabled' => 'danger',
        ][$this->status] ?? 'success';
    }

    /**
     * Get the tautan records for the user
     */
    public function tautan()
    {
        return $this->hasMany('App\Models\Tautan');
    }

    /**
     * Get the count of active tautan for the user
     */
    public function getActiveTautanCountAttribute()
    {
        return $this->tautan()->where('is_active', true)->count();
    }

    /**
     * Get the total count of tautan for the user
     */
    public function getTotalTautanCountAttribute()
    {
        return $this->tautan()->count();
    }
}

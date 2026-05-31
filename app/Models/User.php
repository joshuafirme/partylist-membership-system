<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Basic Auth & Profile
        'name',
        'email',
        'mobile_number',
        'password',

        // System Access & Team
        'user_role_id',
        'team_id',
        'team_role',

        // Demographic & Location Data
        'voter_status',
        'birthday',
        'sex',
        'address',
        'barangay',
        'city',
        'province',
        'precinct_no',
        'occupation',
        'profile_photo_path',

        // e-ID and System Status
        'membership_number',
        'qr_token',
        'status',
        
        // External Integrations
        'registered_from',
        'external_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'qr_token', // Hide QR token from general API responses for security
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'date',
        'password' => 'hashed',
    ];

    /**
     * Get the system role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }

    /**
     * Get the team the user belongs to.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the attendance logs for this user.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * Cast the status to a boolean for easier checking (1 = true, 0 = false)
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Helper to easily get the team leaders
    public function leaders()
    {
        return $this->hasMany(User::class)->whereHas('role', function ($q) {
            $q->where('name', 'Team Leader');
        });
    }

    // If your users are tied to teams via a 'team_id' on the users table, 
    // you can uncomment this relationship:
    /*
    public function users()
    {
        return $this->hasMany(User::class);
    }
    */
}
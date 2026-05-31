<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    // Explicitly define the table since 'user_roles' differs from standard pluralization
    protected $table = 'user_roles';

    protected $fillable = [
        'name',
        'permissions',
    ];

    /**
     * The attributes that should be cast.
     * This automatically converts the JSON string in the database to a PHP array.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * A UserRole has many Users.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'user_role_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'venue',
        'event_date',
        'created_by',
        'status',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'status' => 'boolean',
    ];

    /**
     * The admin/user who created this event.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The attendances logged for this event.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
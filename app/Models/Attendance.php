<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'scanned_by',
        'time_in',
    ];

    /**
     * Cast the time_in column to a proper datetime object
     */
    protected $casts = [
        'time_in' => 'datetime',
    ];

    /**
     * The event this attendance record belongs to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * The member who attended the event.
     */
    public function member()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The staff member/admin who scanned the member's QR code.
     */
    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
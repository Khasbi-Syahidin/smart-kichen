<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    protected $fillable = ['name', 'avatar', 'rfid', 'is_active'];

    protected $hidden = ['created_at', 'updated_at'];

    public function attendanceSessions()
    {
        return $this->belongsToMany(AttendanceSession::class, 'attendance_consumers', 'consumer_id', 'attendance_session_id');
    }
}

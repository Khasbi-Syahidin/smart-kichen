<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceConsumer extends Model
{
    protected $table = 'attendance_consumers';

    protected $fillable = ['consumer_id', 'attendance_session_id'];

    protected $hidden = ['created_at', 'updated_at'];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }
}

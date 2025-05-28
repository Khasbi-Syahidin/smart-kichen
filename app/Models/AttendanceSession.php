<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = ['session', 'menu_id', 'supervisor_id', 'note', 'date'];

    protected $hidden = ['created_at', 'updated_at'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function consumers()
    {
        return $this->belongsToMany(Consumer::class, 'attendance_consumers', 'attendance_session_id', 'consumer_id');
    }
}

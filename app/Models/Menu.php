<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    //
    // add fillable
    protected $fillable = ['name','attachment'];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }
}

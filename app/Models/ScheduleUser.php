<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleUser extends Model
{
    //

    // add fillable
    protected $fillable = ['schedule_id', 'user_id'];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    // protected $hidden = ['created_at', 'updated_at'];

    // hide timestamps
    public $timestamps = false;
}

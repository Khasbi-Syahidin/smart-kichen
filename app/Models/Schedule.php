<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    //

    // add fillable
    protected $fillable = ['day'];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'schedule_users');
    }
}

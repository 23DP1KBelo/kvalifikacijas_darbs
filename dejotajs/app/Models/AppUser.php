<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AppUser extends Authenticatable
{
    protected $table = 'app_users';

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'role',
        'phone_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function danceGroupMembers()
    {
        return $this->hasMany(DanceGroupMember::class, 'user_id', 'id');
    }
}

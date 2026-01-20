<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'role',
        'phone_number',
    ];
}

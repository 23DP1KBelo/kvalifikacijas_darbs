<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanceGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'city',
        'address',
        'picture',
        'approval',
        'status',
        'gener',
        'created_at',
    ];
}

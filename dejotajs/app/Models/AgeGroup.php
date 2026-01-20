<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgeGroup extends Model
{
    protected $fillable = [
        'name',
        'age_group',
        'status_admission',
        'dance_group_id'
    ];
}

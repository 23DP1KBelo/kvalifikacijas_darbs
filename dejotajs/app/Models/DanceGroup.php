<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class DanceGroup extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'city',
        'address',
        'picture',
        'approval',
        'status',
        'genre',
        'created_at',
    ];

    public function ageGroups(){
        return $this->hasMany(AgeGroup::class, 'dance_group_id', 'id');
    }

    public function members(){
        return $this->hasMany(DanceGroupMember::class, 'dance_group_id', 'id');
    }

}

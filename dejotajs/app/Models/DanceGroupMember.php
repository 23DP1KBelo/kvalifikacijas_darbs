<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DanceGroupMember extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'status',
        'role',
        'user_id',
        'age_group_id',
        'dance_group_id',
    ];

    public function appUser(){
        return $this->belongsTo(AppUser::class, 'user_id', 'id');
    }
    public function ageGroup(){
        return $this->belongsTo(AgeGroup::class, 'age_group_id', 'id');
    }
    public function danceGroup(){
        return $this->belongsTo(DanceGroup::class, 'dance_group_id', 'id');
    }

    public function posts() {
        return $this->hasMany( Post::class, 'dance_group_member_id', 'id');
    }

    public function events() {
        return $this->hasMany(Event::class, 'dance_group_member_id', 'id');
    }
}

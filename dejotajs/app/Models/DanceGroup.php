<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DanceGroup extends Model
{
    use SoftDeletes;
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

    public function ageGroups(){
        return $this->hasMany(AgeGroup::class, 'dance_group_id', 'id');
    }

    public function members(){
        return $this->hasMany(DanceGroupMember::class, 'dance_group_id', 'id');
    }

    public function events() {
        return $this->belongsToMany(Event::class, 'dance_groups_in_events', 'dance_group_id', 'event_id');
    }
}

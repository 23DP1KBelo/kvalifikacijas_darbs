<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'location',
        'date',
        'dance_group_member_id',
    ];

    public function member(){
        return $this->belongsTo(DanceGroupMember::class, 'dance_group_member_id', 'id');
    }

    public function danceGroups(){
        return $this->belongsToMany(DanceGroup::class, 'dance_groups_in_events', 'event_id', 'dance_group_id');
    }
}

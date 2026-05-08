<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AgeGroup;
use App\Models\DanceGroupMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Event extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'location',
        'date_start',
        'date_end',
        'dance_group_member_id',
    ];

    public function member(){
        return $this->belongsTo(DanceGroupMember::class, 'dance_group_member_id', 'id');
    }

    public function ageGroups(){
        return $this->belongsToMany(AgeGroup::class, 'dance_groups_in_events', 'event_id', 'age_group_id');
    }
}

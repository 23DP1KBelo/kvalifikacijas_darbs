<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class AgeGroup extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'name',
        'age_group',
        'status_admission',
        'dance_group_id',
    ];

    public function danceGroup(){
        return $this->belongsTo(DanceGroup::class, 'dance_group_id', 'id');
    }

    public function members(){
        return $this->hasMany(DanceGroupMember::class, 'age_group_id', 'id');
    }

    public function admissions(){
        return $this->hasmany(Admission::class, 'age_group_id', 'id');
    }

    public function events() {
        return $this->belongsToMany(Event::class, 'dance_groups_in_events', 'age_group_id', 'event_id');
    }
}

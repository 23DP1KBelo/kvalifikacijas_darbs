<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AgeGroup extends Model
{
    use SoftDeletes;
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

    public function admission(){
        return $this->hasmany(Admission::class, 'age_group_id', 'id');
    }
}

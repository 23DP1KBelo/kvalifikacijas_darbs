<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title', 
        'description',
        'dance_group_member_id',
        'picture',
        'private',
    ];

    public function danceGroupMember(){
        return $this->belongsTo(DanceGroupMember::class, 'dance_group_member_id', 'id');
    }
}

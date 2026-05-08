<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use SoftDeletes;
    use HasFactory;
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

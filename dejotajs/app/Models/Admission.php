<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'age_group_id',
    ];

    public function ageGroup() {
        return $this->belongsTo(AgeGroup::class, 'age_group_id', 'id');
    }
}

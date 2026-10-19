<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DanceGroupMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class AppUser extends Authenticatable
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'app_users';

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'role',
        'phone_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function danceGroupMembers()
    {
        return $this->hasMany(DanceGroupMember::class, 'user_id', 'id');
    }

    protected static function booted(): void
    {
        static::deleting(function ($user) {
            DB::transaction(function () use ($user) {
                $memberships = $user->danceGroupMembers()->get();

                foreach ($memberships as $membership) {
                    $group = DanceGroup::find($membership->dance_group_id);

                    if (!$group) {
                        $membership->delete();
                        continue;
                    }

                    // Pārbauda, vai dzēšamais lietotājs ir vadītājs
                    if ($membership->role === 'leader') {
                        // Pārbauda, vai kolektīvam ir citi vadītāji
                        $otherLeadersExist = $group->members()
                            ->where('role', 'leader')
                            ->where('user_id', '!=', $user->id)
                            ->exists();

                        if (!$otherLeadersExist) {
                            // Nav citu vadītāju — dzēš visus kolektīva dalībniekus
                            $group->members()->delete();
                            // Dzēš pašu kolektīvu
                            $group->delete();
                            continue;
                        }
                    }
                    // Ja ir citi vadītāji, kolektīvu un dejotājus saglabā
                    $membership->delete();
                }
            });
        });
    }
}

<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'leader_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'leader_id');
    }

    public function follow(int $profileId)
    {
        $user = User::find($profileId);
        if ($user && $user->twitter_id != $this->twitter_id) {
            $user->followers()->attach($this->id);
        }

        return $user;
    }

    public function unfollow(int $profileId)
    {
        $user = User::find($profileId);
        if ($user && $user->twitter_id != $this->twitter_id) {
            $user->followers()->detach($this->id);
        }

        return $user;
    }
}

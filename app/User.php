<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];
    protected $table = 'testers';
    public $timestamps = false;
    protected $primaryKey = 'user_id';

    public function getBugs()
    {
        return $this->hasMany(Bug::class, 'author');
    }
}

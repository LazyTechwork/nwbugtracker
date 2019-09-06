<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

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

    public function getModeratableProducts()
    {
        return $this->belongsToMany(Product::class, 'product_moderators', 'product', 'moderator');
    }

    public function moderatorName()
    {
        $req = DB::table('mods')->where('user_id', $this->user_id)->first('funny');
        return $req ?? 'Модератор #' . $this->id;
    }

    public function getVkInfo()
    {
        return DB::table('users')->where('user_id', $this->user_id)->get()[0] ?? null;
    }

    public function VKI()
    {
        return $this->hasOne(VKInfo::class, 'user_id', 'user_id');
    }

    public function isMod()
    {
        return $this->getModeratableProducts()->count() || DB::table('global_moderators')->where('user_id', session()->get('id'))->get()->count() > 0 ? true : false;
    }
}

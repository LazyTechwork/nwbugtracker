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
        $req = DB::table('mods')->where('user_id', $this->user_id);
        return $req->get('funny') ?? 'Модератор #' . $req->get('id');
    }

    public function getVkInfo()
    {
        return DB::table('users')->where('user_id', $this->user_id)->get()[0] ?? null;
    }
}

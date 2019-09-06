<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VKInfo extends Model
{
    protected $guarded = [];
    protected $table = 'users';
    public $timestamps = false;
    protected $primaryKey = 'user_id';

    public function tester()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}

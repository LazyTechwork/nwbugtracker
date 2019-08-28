<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BugUpdate extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'bug_updates';
    protected $dates = ['time'];

    public function getAuthor()
    {
        return $this->belongsTo(User::class, 'author', 'user_id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BugUpdate extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'bug_updates';
}

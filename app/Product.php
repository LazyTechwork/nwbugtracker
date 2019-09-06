<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'products';

    public function getModerators()
    {
        return $this->belongsToMany(User::class, 'product_moderators', 'moderator', 'product');
    }

    public function getBugs()
    {
        return $this->hasMany(Bug::class, 'product');
    }

    public function getLatestVersion()
    {
        return $this->getProductVersions()->whereRaw('"time" <= CURRENT_TIMESTAMP')->orderBy('id', 'desc')->first();
    }

    public function isModerator($id)
    {
        return $this->getModerators->contains(User::find($id));
    }

    public function getProductVersions()
    {
        return $this->hasMany(ProductUpdate::class, 'product')->orderBy('time', 'desc');
    }

    public function getNLdescription() {
        return preg_replace('/<br(\s+)?\/?>/i', "\n", $this->description);
    }

    public function getImage()
    {
        return asset('img/products/' . $this->image);
    }
}

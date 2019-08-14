<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'products';

    public function getModerators()
    {
        return $this->belongsToMany(User::class, 'product_moderators', 'moderator', 'product', 'user_id');
    }

    public function getBugs()
    {
        return $this->hasMany(Bug::class, 'product');
    }

    public function isModerator(User $user)
    {
        return $this->getModerators->contains($user);
    }

    public function getProductVersions()
    {
        return $this->hasMany(ProductUpdate::class, 'product');
    }
}

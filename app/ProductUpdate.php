<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductUpdate extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'product_updates';
    protected $dates = ['time'];

    public function getProduct()
    {
        return $this->belongsTo(Product::class, 'product', 'id');
    }
}

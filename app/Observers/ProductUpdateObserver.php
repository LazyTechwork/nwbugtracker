<?php

namespace App\Observers;

use App\ProductUpdate;

class ProductUpdateObserver
{
    /**
     * Handle the product update "created" event.
     *
     * @param  \App\ProductUpdate  $productUpdate
     * @return void
     */
    public function created(ProductUpdate $productUpdate)
    {
        //
    }

    /**
     * Handle the product update "updated" event.
     *
     * @param  \App\ProductUpdate  $productUpdate
     * @return void
     */
    public function updated(ProductUpdate $productUpdate)
    {
        //
    }

    /**
     * Handle the product update "deleted" event.
     *
     * @param  \App\ProductUpdate  $productUpdate
     * @return void
     */
    public function deleted(ProductUpdate $productUpdate)
    {
        //
    }

    /**
     * Handle the product update "restored" event.
     *
     * @param  \App\ProductUpdate  $productUpdate
     * @return void
     */
    public function restored(ProductUpdate $productUpdate)
    {
        //
    }

    /**
     * Handle the product update "force deleted" event.
     *
     * @param  \App\ProductUpdate  $productUpdate
     * @return void
     */
    public function forceDeleted(ProductUpdate $productUpdate)
    {
        //
    }
}

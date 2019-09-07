<?php

namespace App\Observers;

use App\User;
use ATehnix\VkClient\Client;

class UserObserver
{
    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        if(!$user->isDirty('points')) return;
        $api = new Client('5.101');
        $api->setDefaultToken('aabb1c8e9ab0e61d5c93f02c11b85b257342c79f522c10b0f148bea0501e6bebdc036a02f7d3f01b33e5b');
    }
}

<?php

namespace App\Observers;

use App\Bug;
use ATehnix\VkClient\Client;
use Illuminate\Support\Facades\DB;

class BugObserver
{
    /**
     * Handle the bug "created" event.
     *
     * @param \App\Bug $bug
     * @return void
     */
    public function created(Bug $bug)
    {
        if($bug->priority == 4) return;
        $api = new Client('5.101');
        $api->setDefaultToken('aabb1c8e9ab0e61d5c93f02c11b85b257342c79f522c10b0f148bea0501e6bebdc036a02f7d3f01b33e5b');
        $chats = DB::table('group_notifications')->get();
        foreach ($chats as $chat)
            $api->request('messages.send', [
                'chat_id' => $chat->id,
                'message' => sprintf('[%s] %s %s', $bug->getProduct->name, $bug->name, route('bugs.show', ['id' => $bug->id])),
                'random_id' => $bug->id + 1000000
            ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\User;
use ATehnix\VkClient\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     * @throws \ATehnix\VkClient\Exceptions\VkException
     */
    public function index(Request $request)
    {
        $client = new Client('5.101');
        $client->setDefaultToken(session()->get('vktoken'));
        $user = $client->request('users.get', ['fields' => 'photo_max_orig'])['response'][0];
        $userdb = User::find(session()->get('id'))->first();
        return view('home', compact('user', 'userdb'));
    }
}

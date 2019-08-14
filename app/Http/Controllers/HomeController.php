<?php

namespace App\Http\Controllers;

use App\Product;
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
        $userdb = User::find(session()->get('id'));
        return view('home', compact('user', 'userdb'));
    }

    public function products(Request $request)
    {
        $products = Product::orderBy('id', 'desc')->get();
        return view('products.index', compact('products'));
    }

    public function showProduct(Request $request, $id)
    {
        $prod = Product::find($id);
        $bugs = $prod->getBugs();
        $updates = $prod->getProductVersions;
        return view('products.show', compact('prod', 'bugs', 'updates'));
    }

    public function testers(Request $request)
    {
        $testers = User::paginate(10);
        return view('testers.index', compact('testers'));
    }

    public function showTester(Request $request)
    {

    }
}

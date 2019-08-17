<?php

namespace App\Http\Controllers;

use App\Product;
use App\User;
use ATehnix\VkClient\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

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


//    PRODUCTS

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

    public function showModerators($id)
    {
        $prod = Product::find($id);
        $moders = $prod->getModerators;
        return view('products.moderlist', compact('prod', 'moders'));
    }

    public function addModerator(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'modid' => ['required', 'integer', 'exists:testers,user_id']
        ]);
        if ($validator->fails()) {
            Session::flash('error', 'Мы не нашли данного пользователя в БД тестировщиков!');
            return redirect()->back()->withInput();
        }
        $prod = Product::find($id);
        $user = User::find($request->modid);
        $userinfo = $user->getVkInfo();
//        dd($user->user_id, $userinfo);
        $prod->getModerators()->syncWithoutDetaching($user->user_id);
        Session::flash('success', sprintf('Добавлен пользователь %s в продукт %s в качестве модератора!', $userinfo->last_name . ' ' . $userinfo->first_name, $prod->name));
        return redirect()->back();
    }

    public function delModerator(Request $request, $id, $modid)
    {
        $prod = Product::find($id);
        $user = User::find($modid);
        $userinfo = $user->getVkInfo();
//        dd($user->user_id, $userinfo);
        if (!$prod->isModerator($modid)) {
            Session::flash('error', sprintf('%s не является модератором продукта %s', $userinfo->last_name . ' ' . $userinfo->first_name, $prod->name));
            return redirect()->back();
        }
        $prod->getModerators()->detach($user->user_id);
        Session::flash('success', sprintf('Пользователь %s потерял все права в продукте %s!', $userinfo->last_name . ' ' . $userinfo->first_name, $prod->name));
        return redirect()->back();
    }


//    TESTERS

    public function testers(Request $request)
    {
        $testers = User::paginate(10);
        return view('testers.index', compact('testers'));
    }

    public function showTester(Request $request, $id)
    {
        if ($id == session()->get('id')) return redirect()->route('home');
        $tester = User::find($id);
        if ($tester == null) return redirect()->route('testers.index');
        $vkinfo = $tester->getVkInfo();
        $userdb = User::find(session()->get('id'));
        return view('testers.show', compact('tester', 'vkinfo', 'userdb'));
    }
}

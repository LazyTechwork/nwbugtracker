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
            return redirect()->back()->withInput($request->all());
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

    public function newProductV()
    {
        return view('products.create');
    }

    public function editProductV($id)
    {

    }

    public function newProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'description' => ['required'],
            'img' => ['mimes:mimes:jpeg,png,jpg,svg', 'dimensions:ratio=1/1'],
        ]);
        if ($validator->fails()) {
            Session::flash('error', 'При создании нового продукта произошла ошибка, проверьте все поля!');
            return redirect()->back()->withInput($request->input());
        }
        $dataset = [
            'name' => $request->name,
            'description' => nl2br(e($request->description)),
            'image' => $request->image ?? 'wb.svg',
            'locked' => $request->has('locked'),
        ];
        $prod = Product::create($dataset);
        if ($request->has('img')) {
            $file = $request->img;
            $dataset['image'] = 'prod_' . $prod->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img/products'), $dataset['image']);
            $prod->image = $dataset['image'];
            $prod->save();
        }
        return redirect()->route('products.show', ['id' => $prod->id]);
    }

    public function editProduct(Request $request, $id)
    {

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

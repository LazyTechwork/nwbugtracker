<?php

namespace App\Http\Controllers;

use App\Bug;
use App\BugUpdate;
use App\Product;
use App\ProductUpdate;
use App\User;
use ATehnix\VkClient\Client;
use ATehnix\VkClient\Exceptions\VkException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $user = $client->request('users.get', ['fields' => 'photo_max_orig,photo_200,sex'])['response'][0];
        $userdb = User::find(session()->get('id'));
        $uservk = DB::table('users')->where('user_id', \session()->get('id'))->first(['photo_200', 'first_name', 'last_name', 'sex']);
        if ($uservk->photo_200 != $user['photo_200']) $uservk->photo_200 = $user['photo_200'];
        if ($uservk->first_name != $user['first_name']) $uservk->first_name = $user['first_name'];
        if ($uservk->last_name != $user['last_name']) $uservk->last_name = $user['last_name'];
        if ($uservk->sex != $user['sex']) $uservk->sex = $user['sex'];
        DB::table('users')->where('user_id', \session()->get('id'))->update(json_decode(json_encode($uservk), true));
        return view('home', compact('user', 'userdb'));
    }

    public function terms()
    {
        return view('terms');
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
        $bugs = $prod->getBugs;
        $updates = $prod->getProductVersions;
        $prodstat = [0, 0, 0, 0];
        foreach ($bugs as $bug) {
            $prodstat[0]++;
            switch ($bug->status) {
                case 0:
                case 3:
                    $prodstat[1]++;
                    break;
                case 1:
                    $prodstat[2]++;
                    break;
                case 2:
                    $prodstat[3]++;
                    break;
                default:
                    break;
            }
        }
        return view('products.show', compact('prod', 'prodstat', 'updates'));
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
        Session::flash('success',
            sprintf('Добавлен пользователь «%s» в продукт «%s» в качестве модератора!',
                $userinfo->last_name . ' ' . $userinfo->first_name, $prod->name));
        return redirect()->back();
    }

    public function delModerator(Request $request, $id, $modid)
    {
        $prod = Product::find($id);
        $user = User::find($modid);
        $userinfo = $user->getVkInfo();
//        dd($user->user_id, $userinfo);
        if (!$prod->isModerator($modid)) {
            Session::flash('error',
                sprintf('«%s» не является модератором продукта «%s»',
                    $userinfo->last_name . ' ' . $userinfo->first_name, $prod->name));
            return redirect()->back();
        }
        $prod->getModerators()->detach($user->user_id);
        Session::flash('success',
            sprintf('Пользователь «%s» потерял все права в продукте «%s»!',
                $userinfo->last_name . ' ' . $userinfo->first_name, $prod->name));
        return redirect()->back();
    }

    public function newProductV()
    {
        return view('products.create');
    }

    public function editProductV($id)
    {
        $prod = Product::find($id);
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        if (!session()->get('isglmod')) {
            Session::flash('error', 'Доступ в эту зону запрещён для Вас!');
            return redirect()->route('products.show', ['id' => $id]);
        }

        return view('products.edit', compact('prod'));
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
            'description' => str_replace(["\n", "\r"], "", urlify(nl2br(e($request->description)))),
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
        $prod = Product::find($id);
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        if (!session()->get('isglmod')) {
            Session::flash('error', 'Доступ в эту зону запрещён для Вас!');
            return redirect()->route('products.show', ['id' => $id]);
        }
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'description' => ['required'],
            'img' => ['mimes:mimes:jpeg,png,jpg,svg', 'dimensions:ratio=1/1'],
        ]);
        if ($validator->fails()) {
            Session::flash('error', 'При редактировании продукта произошла ошибка, проверьте все поля!');
            return redirect()->back()->withInput($request->input());
        }
        $dataset = [
            'name' => $request->name,
            'description' => str_replace(["\n", "\r"], "", urlify(nl2br(e($request->description)))),
            'image' => $request->image ?? 'wb.svg',
            'locked' => $request->has('locked'),
        ];
        if ($request->has('img')) {
            $file = $request->img;
            $dataset['image'] = 'prod_' . $prod->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img/products'), $dataset['image']);
        }
        $prod->update($dataset);
        return redirect()->route('products.show', ['id' => $prod->id]);
    }

    public function blockProduct($id)
    {
        $prod = Product::find($id);
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        if (!session()->get('isglmod')) {
            Session::flash('error', 'Доступ в эту зону запрещён для Вас!');
            return redirect()->route('products.show', ['id' => $id]);
        }
        $prod->locked = !$prod->locked;
        $prod->save();
        Session::flash('success', 'Продукт успешно заблокирован/разблокирован!');
        return redirect()->route('products.show', ['id' => $prod->id]);
    }

    public function newUpdateV($id)
    {
        $prod = Product::find($id);
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        if (!$prod->isModerator(session()->get('id')) && !session()->get('isglmod')) {
            Session::flash('error', 'Доступ в эту зону запрещён для Вас!');
            return redirect()->route('products.show', ['id' => $id]);
        }
        return view('products.newupd', compact('prod'));
    }

    public function newUpdate(Request $request, $id)
    {
        $prod = Product::find($id);
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        if (!$prod->isModerator(session()->get('id')) && !session()->get('isglmod')) {
            Session::flash('error', 'Доступ в эту зону запрещён для Вас!');
            return redirect()->route('products.show', ['id' => $id]);
        }
        $validator = Validator::make($request->input(), [
            'version' => ['required', 'max:50'],
            'changelog' => ['required'],
            'time' => ['required', 'date_format:Y-m-d\TH:i', function ($attr, $val, $fail) {
                $val = Carbon::createFromFormat('Y-m-d\TH:i', $val);
                if (!($val > Carbon::now() && $val < Carbon::now()->addDay())) {
                    $fail($attr . 'is before now or greater than one day more');
                }
            }]
        ]);
        if ($validator->fails()) {
            Session::flash('error',
                'Проверьте значения! Напоминаем, версия должна быть не больше 50 символов в длину, а время не меньше текущего и не больше одного дня позже');
            return redirect()->back()->withInput();
        }
        $dataset = [
            'product' => $id,
            'version' => $request->version,
            'changelog' => str_replace(["\n", "\r"], "", urlify(nl2br(e($request->changelog)))),
            'time' => Carbon::createFromFormat('Y-m-d\TH:i', $request->time)->getTimestamp()
        ];
        $upd = ProductUpdate::create($dataset);
        Session::flash('success',
            sprintf('Мы добавили обновление продукта с версией «%s», которое будет доступно «%s»',
                $upd->version, Carbon::make($upd->time)->format('d.m.Y H:i')));
        return redirect()->route('products.show', ['id' => $id]);
    }

    public function delUpdate(Request $request, $id, $updateid)
    {
        $prod = Product::find($id);
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        if (!$prod->isModerator(session()->get('id')) && !session()->get('isglmod')) {
            Session::flash('error', 'Доступ в эту зону запрещён для Вас!');
            return redirect()->route('products.show', ['id' => $id]);
        }
        $upd = ProductUpdate::find($updateid);
        if ($upd == null) {
            Session::flash('error', 'Обновление продукта не найдено!');
            return redirect()->route('products.show', ['id' => $id]);
        }
        if (!$upd->time->gte(\Carbon\Carbon::now()->addHour()) && !session()->get('isglmod')) {
            Session::flash('error',
                sprintf('Удалить обновление продукта с версией «%s» невозможно, т.к. прошло более часа. Попросите об удалении у администрации баг-трекера',
                    $upd->version));
            return redirect()->route('products.show', ['id' => $id]);
        }
        if ($upd->product != $id) {
            Session::flash('error',
                sprintf('Обновление продукта с версией «%s» не относится к продукту «%s»',
                    $upd->version, $prod->name));
            return redirect()->route('products.show', ['id' => $id]);
        }
        $upd->delete();
        Session::flash('success',
            sprintf('Обновление продукта с версией «%s» удалено в продукте «%s»', $upd->version, $prod->name));
        return redirect()->route('products.show', ['id' => $id]);
    }


//    TESTERS

    public function testers(Request $request)
    {
        $testers = User::withCount('getBugs')->with('VKI')->join('users', 'testers.user_id', '=', 'users.user_id')->orderBy('get_bugs_count', 'desc')->orderBy('users.last_name');
        if ($request->has('s'))
            $testers = $testers->where('users.last_name', 'like', '%' . e($request->s) . '%')->orWhere('users.first_name', 'like', '%' . e($request->s) . '%')->paginate(10);
        else
            $testers = $testers->paginate(10);
        return view('testers.index', compact('testers'));
    }

    public function showTester(Request $request, $id)
    {
        if ($id == session()->get('id')) return redirect()->route('home');
        $tester = User::find($id);
        if ($tester == null) return redirect()->route('testers.index');
        $vkinfo = $tester->getVkInfo();
        $userdb = User::find(session()->get('id'));
        $bugs = Bug::where('author', $tester->user_id)->orderBy('created_at', 'desc')->take(3)->get();
        return view('testers.show', compact('tester', 'vkinfo', 'userdb', 'bugs'));
    }

//    REPORTS

    public function bugs()
    {
        $bugs = Bug::orderBy('created_at', 'desc')->paginate(15);
        $btype = Bug::$bugtypes['all'];
        return view('bugs.index', compact('bugs', 'btype'));
    }

    public function productBugs($id)
    {
        $prod = Product::find($id);
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден');
            return redirect()->route('products.index');
        }
        $bugs = Bug::where('product', $id)->orderBy('created_at', 'desc')->paginate(15);
        $btype = Bug::$bugtypes['product'];
        $btype['pars'] = ['id' => $id];
        $btype['tab'] = sprintf($btype['tab'], $prod->name);
        return view('bugs.index', compact('bugs', 'btype'));
    }

    public function myBugs()
    {
        $bugs = Bug::where('author', \session()->get('id'))->orderBy('created_at', 'desc')->paginate(15);
        $btype = Bug::$bugtypes['my'];
        return view('bugs.index', compact('bugs', 'btype'));
    }

    public function testerBugs($id)
    {
        $usr = User::find($id);
        if ($usr == null) {
            Session::flash('error', 'Тестировщик не найден');
            return redirect()->route('testers.index');
        }
        $usrinfo = $usr->getVkInfo();
        $bugs = Bug::where('author', $id)->orderBy('created_at', 'desc')->paginate(15);
        $btype = Bug::$bugtypes['tester'];
        $btype['pars'] = ['id' => $id];
        $btype['tab'] = sprintf($btype['tab'], $usrinfo->last_name . ' ' . $usrinfo->first_name);
        return view('bugs.index', compact('bugs', 'btype'));
    }

    public function newBugV($productid)
    {
        $prod = Product::find($productid);
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        if ($prod->locked) {
            Session::flash('error', 'Продукт блокирован, у Вас нет доступа к созданию отчётов здесь!');
            return redirect()->route('products.show', ['id' => $productid]);
        }
        if ($prod->getLatestVersion() == null) {
            Session::flash('error',
                'У продукта нет опубликованных версий, попросите администратора добавить хотябы одну версию продукта!');
            return redirect()->route('products.show', ['id' => $productid]);
        }

        return view('bugs.newbug', compact('prod'));
    }

    public function newBug(Request $request, $productid)
    {
        $prod = Product::find($productid);
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        if ($prod->locked) {
            Session::flash('error', 'Продукт блокирован, у Вас нет доступа к созданию отчётов здесь!');
            return redirect()->route('products.show', ['id' => $productid]);
        }
        if ($prod->getLatestVersion() == null) {
            Session::flash('error',
                'У продукта нет опубликованных версий, попросите администратора добавить хотябы одну версию продукта!');
            return redirect()->route('products.show', ['id' => $productid]);
        }
        $validator = Validator::make($request->input(), [
            'name' => ['required'],
            'steps' => ['required'],
            'actually' => ['required', 'max:450'],
            'expectedly' => ['required', 'max:450'],
            'type' => ['required', 'digits_between:0,7'],
            'priority' => ['required', 'digits_between:0,4']
        ]);

        if ($validator->fails()) {
            Session::flash('error', 'Проверьте значения полей!');
            return redirect()->back()->withInput();
        }
        $dataset = [
            'product' => $productid,
            'author' => session()->get('id'),
            'name' => $request->name,
            'version' => $prod->getLatestVersion()->id,
            'steps' => str_replace(["\n", "\r"], "", urlify(nl2br(e($request->steps)))),
            'actually' => $request->actually,
            'expectedly' => $request->expectedly,
            'type' => $request->type,
            'priority' => $request->priority
        ];
        $bugid = Bug::create($dataset);
        Session::flash('success', sprintf('Создан новый отчёт «%s» для продукта «%s»', $dataset['name'], $prod->name));
        return redirect()->route('bugs.show', ['id' => $bugid]);
    }

    public function editBugV($id)
    {
        $bug = Bug::find($id);
        if ($bug == null) {
            Session::flash('error', 'Отчёт не найден!');
            return redirect()->route('home');
        }
        $prod = $bug->getProduct;
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        $author = $bug->getAuthor;
        if ($author->user_id != session()->get('id')) {
            Session::flash('error', 'У Вас недостаточно прав для изменения данного отчёта');
            return redirect()->route('bugs.show', ['id' => $id]);
        }
        if (!$bug->canBeReopened() && $bug->status != 0) {
            Session::flash('error', 'Невозможно редактировать отчёт!');
            return redirect()->route('bugs.show', ['id' => $id]);
        }
        return view('bugs.editbug', compact('bug', 'prod'));
    }

    public function editBug(Request $request, $id)
    {
        $bug = Bug::find($id);
        if ($bug == null) {
            Session::flash('error', 'Отчёт не найден!');
            return redirect()->route('home');
        }
        $prod = $bug->getProduct;
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        $author = $bug->getAuthor;
        if ($author->user_id != session()->get('id')) {
            Session::flash('error', 'У Вас недостаточно прав для изменения данного отчёта');
            return redirect()->route('bugs.show', ['id' => $id]);
        }
        if (!$bug->canBeReopened() && $bug->status != 0) {
            Session::flash('error', 'Невозможно редактировать отчёт!');
            return redirect()->route('bugs.show', ['id' => $id]);
        }
        $validator = Validator::make($request->input(), [
            'name' => ['required'],
            'steps' => ['required'],
            'actually' => ['required', 'max:450'],
            'expectedly' => ['required', 'max:450'],
            'type' => ['required', 'digits_between:0,7'],
            'priority' => ['required', 'digits_between:0,4']
        ]);

        if ($validator->fails()) {
            Session::flash('error', 'Проверьте значения полей!');
            return redirect()->back()->withInput();
        }
        $dataset = [
            'name' => $request->name,
            'version' => $prod->getLatestVersion()->id,
            'steps' => str_replace(["\n", "\r"], "", urlify(nl2br(e($request->steps)))),
            'actually' => $request->actually,
            'expectedly' => $request->expectedly,
            'type' => $request->type,
            'priority' => $request->priority
        ];
        if ($bug->canBeReopened()) {
            $dataset['status'] = 3;
            BugUpdate::create([
                'bug_id' => $id,
                'author' => \session()->get('id'),
                'status' => 3,
                'comment' => 'Отчёт отредактирован и автоматически был переоткрыт',
                'time' => Carbon::now()->toDateTimeString(),
                'hidden' => false
            ]);
        }
        $bug->update($dataset);
        Session::flash('success', sprintf('Отчёт «%s» для продукта «%s» отредактирован', $dataset['name'], $prod->name));
        return redirect()->route('bugs.show', ['id' => $bug->id]);
    }

    public function showBug($id)
    {
        $bug = Bug::find($id);
        if ($bug == null) {
            Session::flash('error', 'Отчёт не найден!');
            return redirect()->route('home');
        }
        $prod = $bug->getProduct;
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        $author = $bug->getAuthor;
        if ($bug->getPriority() == 'Уязвимость' && !($prod->isModerator(session()->get('id')) ||
                session()->get('isglmod') || $author->user_id == session()->get('id'))) {
            Session::flash('error', 'Мы не можем отобразить Вам данный отчёт!');
            return redirect()->route('home');
        }
        $author = $author->getVkInfo();
        $updates = $bug->getBugUpdates;
        return view('bugs.show', compact('bug', 'prod', 'author', 'updates'));
    }

    public function deleteBug($id)
    {
        $bug = Bug::find($id);
        if ($bug == null) {
            Session::flash('error', 'Отчёт не найден!');
            return redirect()->route('home');
        }
        $prod = $bug->getProduct;
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        $author = $bug->getAuthor;
        if (!($prod->isModerator(session()->get('id')) || session()->get('isglmod') || ($author->user_id == session()->get('id') && ($bug->canBeReopened() || $bug->status == 0 || $bug->status == 3)))) {
            Session::flash('error', 'У Вас недостаточно прав для удаления данного отчёта');
            return redirect()->route('bugs.show', ['id' => $id]);
        }
        $bug->delete();
        Session::flash('success', 'Отчёт удален!');
        return redirect()->route('bugs.index');
    }

    public function updateBug(Request $request, $id)
    {
        $bug = Bug::find($id);
        if ($bug == null) {
            Session::flash('error', 'Отчёт не найден!');
            return redirect()->route('home');
        }
        $prod = $bug->getProduct;
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        $author = $bug->getAuthor;
        if (!($prod->isModerator(session()->get('id')) || session()->get('isglmod') || ($author->user_id == session()->get('id') && $bug->canBeReopened()))) {
            Session::flash('error', 'У Вас недостаточно прав для изменения статуса данного отчёта');
            return redirect()->route('bugs.show', ['id' => $id]);
        }
        $reopening = ($author->user_id == session()->get('id') && $bug->canBeReopened()) && !($prod->isModerator(session()->get('id')) || session()->get('isglmod'));
        if ($reopening)
            $request->merge(['status' => 3, 'reward' => 0]);
        $validator = Validator::make($request->input(), [
            'status' => ['required', 'digits_between:0,10'],
            'reward' => ['required', 'numeric', 'gte:0', 'lte:10000']
        ]);
        if ($validator->fails()) {
            Session::flash('error', 'Вы указали неверный статус или неправильно ввели вознаграждение (от 0 до 10000)!');
            return redirect()->route('bugs.show', ['id' => $id]);
        }

        BugUpdate::create([
            'bug_id' => $id,
            'author' => \session()->get('id'),
            'status' => $request->status,
            'comment' => $request->comment != null ? e($request->comment) : null,
            'time' => Carbon::now()->toDateTimeString(),
            'hidden' => !$reopening || $request->hideauthor
        ]);
        $bug->status = $request->status;
        if ($bug->reward != -1 && !$reopening) $bug->reward = $request->reward;
        $bug->save();
        Session::flash('success', 'Статус отчёта успешно изменен');
        return redirect()->route('bugs.show', ['id' => $id]);
    }

    public function actualize($id, $actual)
    {
        $bug = Bug::find($id);
        if ($bug == null) {
            Session::flash('error', 'Отчёт не найден!');
            return redirect()->route('home');
        }
        $prod = $bug->getProduct;
        if ($prod == null) {
            Session::flash('error', 'Продукт не найден!');
            return redirect()->route('home');
        }
        $author = $bug->getAuthor;
        if (!(!$bug->isActualVersion() && $author->user_id == session()->get('id') && $bug->canBeReopened())) {
            Session::flash('error', 'У Вас недостаточно прав для изменения статуса данного отчёта');
            return redirect()->route('bugs.show', ['id' => $id]);
        }
        if ($actual != 1) {
            BugUpdate::create([
                'bug_id' => $id,
                'author' => $author->user_id,
                'status' => 4,
                'comment' => 'Закрыт по причине не актуальности',
                'time' => Carbon::now()->toDateTimeString(),
                'hidden' => false
            ]);
            $bug->status = 4;
            $bug->reward = 0;
            $bug->save();
            Session::flash('success', 'Отчёт закрыт');
            return redirect()->route('bugs.show', ['id' => $id]);
        } else {
            $bug->version = $prod->getLatestVersion()->id;
            $bug->save();
            Session::flash('success', 'Помечено как актуально');
            return redirect()->route('bugs.show', ['id' => $id]);
        }
    }


//    SHOP

    public function shopIndex()
    {
        return view('shop.index');
    }

    public function settings()
    {

    }

    public function apanel()
    {
        $bugs = Bug::where('reward', '>', '0')->whereIn('status', [2,4])->get();
        $bugsawait = $bugs->count();
        $testersawait = 0;
        $pointsawait = 0;
        $testers = [];
        $tstrbugs = [];
        $points = [];
        foreach ($bugs as $bug) {
            $pointsawait += $bug->reward;
            array_push($testers, $bug->author);
            if (array_key_exists($bug->author, $points))
                $points[$bug->author] += $bug->reward;
            else
                $points[$bug->author] = $bug->reward;
            if (array_key_exists($bug->author, $tstrbugs))
                array_push($tstrbugs[$bug->author], $bug);
            else
                $tstrbugs[$bug->author] = [$bug];
        }
        $testers = array_unique($testers);
        $tstrs = User::find($testers);
        $testersawait = count($testers);
        return view('apanel', compact('bugsawait', 'testersawait', 'pointsawait', 'tstrs', 'points', 'tstrbugs'));
    }

    public function letPoints()
    {
        $bugs = Bug::where('reward', '>', '0')->whereIn('status', [2,4])->get();
        $testers = [];
        $points = [];
        $pointsawait = 0;
        foreach ($bugs as $bug) {
            $pointsawait += $bug->reward;
            if (!array_key_exists($bug->author, $testers))
                $testers[$bug->author] = $bug->getAuthor;
            if (array_key_exists($bug->author, $points))
                $points[$bug->author] += $bug->reward;
            else
                $points[$bug->author] = $bug->reward;
            $bug->reward = 0;
            $bug->save();
        }
        $api = new Client('5.101');
        $api->setDefaultToken('aabb1c8e9ab0e61d5c93f02c11b85b257342c79f522c10b0f148bea0501e6bebdc036a02f7d3f01b33e5b');
        foreach ($testers as $tester) {
            $tester->points += $points[$tester->user_id];
            $tester->save();
            try {
                $api->request('messages.send', [
                    'user_id' => $tester->user_id,
                    'message' => sprintf('Сообщество @nwbugs (NeoWave Bug-tracker) начислило Вам %d баллов. Сейчас у Вас: %d баллов', $points[$tester->user_id], $tester->points),
                    'random_id' => random_int(PHP_INT_MIN, PHP_INT_MAX)
                ]);
            } catch (VkException $e) {
            }
        }
        Session::flash('success', 'Начислено ' . $pointsawait . ' баллов ' . count($testers) . ' тестировщикам!');
        return redirect()->route('apanel');
    }

    public function modpanel()
    {
        $moderator = User::find(\session()->get('id'));
        if (\session()->get('isglmod'))
            $products = Product::all();
        else
            $products = $moderator->getModeratableProducts;
        $prodids = array_column($products->map->only('id')->toArray(), 'id');
        $open = Bug::whereIn('product', $prodids)->whereIn('status', [0, 3])->get();
        $inqueue = Bug::whereIn('product', $prodids)->where('status', 5)->get();
        $wip = Bug::whereIn('product', $prodids)->where('status', 1)->get();
        return view('modpanel.mpanel', compact('products', 'moderator', 'open', 'inqueue', 'wip'));
    }
}

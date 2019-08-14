<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use ATehnix\VkClient\Auth;
use ATehnix\VkClient\Client;
use ATehnix\VkClient\Exceptions\VkException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(Request $request)
    {
        if (Session::has('vktoken')) return redirect()->route('home');
        $auth = new Auth('7096318', 'GUvmMOIYmfXgziiWNg5m', route('authvk'), 'offline,notify');
        return view('auth.login', compact('auth'));
    }

    public function authvk(Request $request)
    {
        if (Session::has('vktoken')) return redirect()->route('home');
        if (!$request->has('code')) return redirect()->route('login');
        $auth = new Auth('7096318', 'GUvmMOIYmfXgziiWNg5m', route('authvk'), 'offline,notify');
        try {
            $token = $auth->getToken($request->code);
            $client = new Client('5.101');
            $client->setDefaultToken($token);
            $user_id = $client->request('users.get')['response'][0]['id'];
            if (User::where('user_id', $user_id)->get()->count() > 0) {
                session()->put(['vktoken' => $token,'id'=>$user_id]);
                return redirect()->route('home');
            } else {
                return redirect()->route('login')->with(['error' => 'Мы не нашли Вас в базе данных тестировщиков. Увы, Вам бан! *шучу*']);
            }
        } catch (VkException $e) {
            return redirect()->route('login')->with(['error' => $e]);
        }
    }

    public function logout(Request $request)
    {
        session()->remove('vktoken');
        session()->remove('id');
        session()->flush();
        return redirect()->route('login')->with(['success' => 'Мы удалили всю конфиденциальную информацию о Вас, поэтому не беспокойтесь за Ваши данные. Удачи в жизни!']);
    }
}

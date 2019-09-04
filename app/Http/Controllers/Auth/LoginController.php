<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use ATehnix\VkClient\Auth;
use ATehnix\VkClient\Client;
use ATehnix\VkClient\Exceptions\VkException;
use Carbon\Carbon;
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
        $this->middleware('guest')->except('logout', 'welcome');
    }

    public function showLoginForm(Request $request)
    {
        if (Session::has('vktoken')) return redirect()->route('home');
        $auth = new Auth('7096318', 'GUvmMOIYmfXgziiWNg5m', route('authvk'));
        return view('auth.login', compact('auth'));
    }

    public function authvk(Request $request)
    {
        if (Session::has('vktoken')) return redirect()->route('home');
        if (!$request->has('code')) return redirect()->route('login');
        $auth = new Auth('7096318', 'GUvmMOIYmfXgziiWNg5m', route('authvk'));
        try {
            $data = $auth->getUserData($request->code);
            if (!isset($data['access_token'])) {
                throw new VkException('The access token is not present in the API response.');
            }
            $token = $data['access_token'];
            $expire = $data['expires_in'];
            $expire = Carbon::now()->addSeconds($expire)->timestamp;
            $user_id = $data['user_id'];
            $client = new Client('5.101');
            $client->setDefaultToken($token);
            if (User::where('user_id', $user_id)->get()->count() > 0) {
                session()->put(['vktoken' => $token, 'id' => $user_id, 'expire' => $expire]);
//                dd($expire, Carbon::createFromTimestamp($expire)->format('d.m.Y H:i:s'));
                return redirect()->route('home')->with(['success' => 'Вы успешно авторизовались. Время истечения сессии: ' . Carbon::createFromTimestamp($expire)->format('d.m.Y H:i:s')]);
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

    public function welcome()
    {
        return view('welcome');
    }
}

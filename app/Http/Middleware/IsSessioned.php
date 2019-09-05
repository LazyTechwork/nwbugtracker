<?php

namespace App\Http\Middleware;

use App\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class IsSessioned
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('vktoken') && \session()->has('expire')) {
            if (\session()->get('expire') < Carbon::now()->timestamp) {
                \session()->flush();
                return redirect()->route('login')->with(['error' => 'К сожалению время сессии истекло, это сделано в целях безопасности. Пожалуйста авторизуйтесь заново!']);
            }
            $whitelist = [362551208, 538327743, 242521347, 334555354];
            $whitelist_enabled = true;
            if ($whitelist_enabled && !in_array(\session()->get('id'), $whitelist)) {
                Session::flush();
                Session::flash('error', 'К сожалению Баг-трекер сейчас доступен только избранным тестировщикам!');
                return redirect()->route('login');
            }
            $tester = User::find(\session()->get('id'));
            if ($tester->kick)
                if ($request->routeIs('home')) return $next($request); else {
                    Session::flash('error', 'Доступ к данному разделу для Вас запрещен, так как Вы исключены из программы тестирования');
                    return redirect()->route('home');
                }

            $isglmod = DB::table('global_moderators')->where('user_id', session()->get('id'))->get()->count() > 0;
            session()->put(['isglmod' => $isglmod]);
            return $next($request);
        } else {
            \session()->flush();
            return redirect()->route('login')->with(['error' => 'Вы не авторизованы!']);
        }
    }
}

<?php

namespace App\Http\Middleware;

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
            if(\session()->get('expire') < Carbon::now()->timestamp) {
                \session()->flush();
                return redirect()->route('login')->with(['error' => 'К сожалению время сессии истекло, это сделано в целях безопасности. Пожалуйста авторизуйтесь заново!']);
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

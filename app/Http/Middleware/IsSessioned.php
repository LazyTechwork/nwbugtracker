<?php

namespace App\Http\Middleware;

use Closure;
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
        if (session()->has('vktoken'))
            return $next($request);
        else
            return redirect()->route('login')->with(['error' => 'Вы не авторизованы!']);
    }
}

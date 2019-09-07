<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class IsModerator
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
        $tester = User::find(session()->get('id'));
        if (session()->get('isglmod') || $tester->isMod())
            return $next($request);
        else
            return redirect()->route('home');
    }
}

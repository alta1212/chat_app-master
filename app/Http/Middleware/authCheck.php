<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class authCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!session()->has('id'))
        {
            return redirect("/")->with("loi","You must login first");
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class AuthorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      // default authentication check for Author
      if (Auth::check() && Auth::user()->role->id == 2){
        return $next($request);
      }
      else {
        return redirect()->route('login');
      }
      //  return $next($request);
    }
}

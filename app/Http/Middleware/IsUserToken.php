<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsUserToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $IdUsuario = $request->header('id_usuario');

        if(auth('api')->user()->id == $IdUsuario)
            return $next($request);

        return response(['message' => 'Invalid User'],403);
    }
}

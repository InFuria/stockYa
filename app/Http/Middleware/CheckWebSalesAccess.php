<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

class CheckWebSalesAccess
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
        $isAdmin = Gate::inspect('isAdmin', request()->user())->allowed();
        $isOwner = Gate::inspect('isOwner', [request()->user(), request()->order])->allowed();

        if ($isAdmin == false || ($isOwner == false && $isAdmin == false))
            return response()->json('Usuario no autorizado.',400);

        return $next($request);
    }
}

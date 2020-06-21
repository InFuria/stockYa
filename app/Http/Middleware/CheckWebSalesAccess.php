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
        $user = request()->user();
        if (!$user->isSeller() && !$user->isAdmin())
            return response()->json(['message' => 'No posee permisos para acceder a este registro'],403);

        return $next($request);
    }
}

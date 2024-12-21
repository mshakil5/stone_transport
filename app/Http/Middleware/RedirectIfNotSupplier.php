<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotSupplier
{
    public function handle($request, Closure $next, $guard = 'supplier')
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('supplier.login');
        }

        return $next($request);
    }
}


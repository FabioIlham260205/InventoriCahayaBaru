<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireShopAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('shop_authenticated')) {
            return redirect()->route('shop.login');
        }

        return $next($request);
    }
}

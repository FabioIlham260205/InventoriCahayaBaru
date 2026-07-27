<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireInventoryAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('inventory_authenticated')) {
            return redirect()->route('inventory.login');
        }

        return $next($request);
    }
}

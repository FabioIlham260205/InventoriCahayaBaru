<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopAuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('shop_authenticated')) {
            return redirect()->route('shop.index');
        }

        return view('shop.auth.login');
    }

    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $clientId = (string) config('inventory_auth.google.client_id');

        if ($clientId === '') {
            return back()->withErrors(['oauth' => 'Konfigurasi Google OAuth belum lengkap di file .env.']);
        }

        $state = Str::random(40);
        $request->session()->put('oauth_state', $state);
        $request->session()->put('oauth_context', 'shop');

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => config('inventory_auth.google.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ], '', '&', PHP_QUERY_RFC3986);

        return redirect()->away(config('inventory_auth.google.authorize_url').'?'.$query);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['shop_authenticated', 'shop_user']);
        $request->session()->regenerate();

        return redirect()->route('shop.login')->with('status', 'Anda telah keluar dari e-commerce.');
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('inventory_authenticated')) {
            return redirect()->route('inventory.index');
        }

        return view('auth.login');
    }

    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $clientId = (string) config('inventory_auth.google.client_id');

        if ($clientId === '') {
            return back()->withErrors(['oauth' => 'Konfigurasi Google OAuth belum lengkap di file .env.']);
        }

        $state = Str::random(40);
        $request->session()->put('oauth_state', $state);
        $request->session()->put('oauth_context', 'inventory');

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

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()->route('inventory.login')->withErrors(['oauth' => 'Login Google dibatalkan atau ditolak.']);
        }

        $state = $request->session()->pull('oauth_state');
        $context = $request->session()->pull('oauth_context', 'inventory');

        if (! $state || ! hash_equals($state, (string) $request->query('state'))) {
            return redirect()->route('inventory.login')->withErrors(['oauth' => 'Sesi login OAuth tidak valid. Silakan coba lagi.']);
        }

        $code = (string) $request->query('code');

        if ($code === '') {
            return redirect()->route('inventory.login')->withErrors(['oauth' => 'Kode otorisasi Google tidak ditemukan.']);
        }

        $tokenResponse = Http::asForm()->post(config('inventory_auth.google.token_url'), [
            'client_id' => config('inventory_auth.google.client_id'),
            'client_secret' => config('inventory_auth.google.client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => config('inventory_auth.google.redirect_uri'),
        ]);

        if ($tokenResponse->failed() || ! $tokenResponse->json('access_token')) {
            return redirect()->route('inventory.login')->withErrors(['oauth' => 'Gagal menukar kode OAuth dengan token Google.']);
        }

        $userResponse = Http::withToken($tokenResponse->json('access_token'))
            ->get(config('inventory_auth.google.userinfo_url'));

        if ($userResponse->failed() || ! $userResponse->json('email')) {
            return redirect()->route('inventory.login')->withErrors(['oauth' => 'Gagal mengambil profil pengguna Google.']);
        }

        $email = (string) $userResponse->json('email');
        $name = (string) $userResponse->json('name');
        $picture = $userResponse->json('picture');

        if (empty($picture)) {
            // Gunakan inisial nama sebagai avatar jika foto tidak didapatkan dari Google
            $picture = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=random&color=fff&size=200";
        }

        $request->session()->regenerate();

        if ($context === 'shop') {
            $request->session()->put('shop_authenticated', true);
            $request->session()->put('shop_user', [
                'name' => $name,
                'email' => $email,
                'picture' => $picture,
            ]);

            return redirect()->intended(route('shop.index'))->with('status', 'Login e-commerce berhasil. Selamat berbelanja.');
        }

        $request->session()->put('inventory_authenticated', true);
        $request->session()->put('inventory_user', [
            'name' => $name,
            'email' => $email,
            'picture' => $picture,
        ]);

        return redirect()->intended(route('inventory.index'))->with('status', 'Login berhasil.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['inventory_authenticated', 'inventory_user', 'oauth_state']);
        $request->session()->regenerate();

        return redirect()->route('inventory.login')->with('status', 'Logout inventory berhasil.');
    }
}

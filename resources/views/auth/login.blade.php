<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Inventory Cahaya Baru</title>
    <style>
        :root { color-scheme: light; --ink:#17202a; --muted:#657482; --line:#d7dee6; --panel:#ffffff; --bg:#f6f8fb; --green:#2f7d4f; --red:#b42318; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; display:grid; place-items:center; padding:24px; font-family:Arial, Helvetica, sans-serif; color:var(--ink); background:var(--bg); }
        .login { width:100%; max-width:480px; background:var(--panel); border:1px solid var(--line); border-radius:8px; padding:24px; box-shadow:0 2px 12px rgba(0,0,0,.06); }
        h1 { margin:0 0 6px; font-size:28px; letter-spacing:0; }
        p { margin:0 0 20px; color:var(--muted); line-height:1.5; }
        label { display:block; font-size:13px; font-weight:700; margin-bottom:6px; }
        input { width:100%; border:1px solid var(--line); border-radius:6px; padding:11px 12px; font:inherit; background:#fff; }
        .field { margin-bottom:14px; }
        .button { width:100%; border:1px solid var(--green); border-radius:6px; padding:11px 12px; color:#fff; background:var(--green); cursor:pointer; font:inherit; font-weight:700; }
        .notice { margin-bottom:16px; border:1px solid #b7e4c7; color:#1b5e37; background:#ecfdf3; border-radius:8px; padding:12px; }
        .errors { margin-bottom:16px; border:1px solid #fecaca; color:#991b1b; background:#fff1f2; border-radius:8px; padding:12px; }
    </style>
</head>
<body>
    <main class="login">
        <h1>Login inventory</h1>
        <p>Masuk menggunakan akun Google yang terhubung ke OAuth client di file .env.</p>

        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ route('inventory.login.store') }}">
            @csrf
            <button class="button" type="submit">Masuk dengan Google</button>
        </form>
        <p style="text-align:center;margin-top:16px;"><a style="color:var(--green);font-weight:700;text-decoration:none;" href="{{ route('shop.login') }}">Login e-commerce</a></p>
    </main>
</body>
</html>

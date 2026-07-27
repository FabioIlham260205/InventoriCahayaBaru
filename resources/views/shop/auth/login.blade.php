<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk E-Commerce | Cahaya Baru</title>
    <style>body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;box-sizing:border-box;font-family:Arial,sans-serif;background:#f6f8fb;color:#17202a}.card{width:100%;max-width:480px;padding:28px;box-sizing:border-box;background:#fff;border:1px solid #d7dee6;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.06)}h1{margin:0 0 8px;font-size:27px}p{color:#657482;line-height:1.5;margin:0 0 20px}label{display:block;font-size:13px;font-weight:700;margin:14px 0 6px}input{width:100%;padding:11px;box-sizing:border-box;border:1px solid #d7dee6;border-radius:6px;font:inherit}.button{width:100%;margin-top:20px;padding:12px;border:0;border-radius:6px;background:#15803d;color:#fff;font-weight:700;font:inherit;cursor:pointer}.notice,.errors{padding:12px;border-radius:7px;margin-bottom:15px}.notice{background:#ecfdf3;color:#166534}.errors{background:#fff1f2;color:#991b1b}a{color:#15803d;font-weight:700;text-decoration:none}.footer{text-align:center;margin-top:18px;font-size:14px}</style>
</head>
<body><main class="card"><h1>Masuk e-commerce</h1><p>Masuk dengan akun Google untuk berbelanja di Cahaya Baru dan melanjutkan pesanan Anda.</p>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<form method="post" action="{{ route('shop.login.store') }}">@csrf
<button class="button" type="submit">Masuk dengan Google</button></form>
<p class="footer"><a href="{{ route('inventory.login') }}">Login inventory</a></p></main></body></html>

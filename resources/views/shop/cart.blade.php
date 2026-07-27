<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang Belanja | Cahaya Baru</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root{
            --green:#009846;--green-light:#dcfce7;--green-dark:#007237;
            --mint:#e9f8ef;--ink:#1d2420;--muted:#7b827e;--line:#e8ece9;
            --bg:linear-gradient(160deg,#e9f8ef 0%,#f5f7f5 40%,#f0faf4 100%);
            --card:#ffffff;--radius:14px;--radius-sm:10px;
            --shadow:0 2px 8px rgba(0,0,0,.06);--shadow-md:0 8px 24px rgba(0,0,0,.08);
            --font:'Inter',system-ui,-apple-system,sans-serif;
        }
        *{box-sizing:border-box;margin:0}
        body{font-family:var(--font);color:var(--ink);background:var(--bg);min-height:100vh;-webkit-font-smoothing:antialiased}
        a{color:inherit;text-decoration:none}

        .head{background:linear-gradient(135deg,#009846 0%,#00b855 50%,#007237 100%);color:#fff;padding:0}
        .head-inner{max-width:1200px;margin:0 auto;padding:20px 20px 42px;display:flex;justify-content:space-between;align-items:center;gap:14px}
        .hello b{font-size:16px;letter-spacing:-.02em}.hello small{opacity:.8;font-size:13px;display:block;margin-top:2px}
        .avatar{width:42px;height:42px;border:2px solid rgba(255,255,255,.4);border-radius:50%;display:grid;place-items:center;font-size:18px;background:rgba(255,255,255,.15);backdrop-filter:blur(8px)}

        .shell{max-width:800px;margin:0 auto;padding:12px 20px 110px}
        .notice,.errors{padding:14px 16px;border-radius:var(--radius-sm);margin-bottom:16px;font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px}
        .notice{background:linear-gradient(135deg,#dcfce7,#bbf7d0);color:#166534;border:1px solid #86efac}
        .errors{background:linear-gradient(135deg,#fee2e2,#fecaca);color:#991b1b;border:1px solid #fca5a5}
        .notice::before{content:'✓';width:22px;height:22px;border-radius:50%;background:#16a34a;color:#fff;display:grid;place-items:center;font-size:12px;flex-shrink:0}
        .errors::before{content:'!';width:22px;height:22px;border-radius:50%;background:#dc2626;color:#fff;display:grid;place-items:center;font-size:14px;font-weight:900;flex-shrink:0}

        .back{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border-radius:var(--radius-sm);background:rgba(255,255,255,.7);backdrop-filter:blur(8px);border:1px solid var(--line);font-weight:700;font-size:13px;color:var(--muted);margin-bottom:22px;transition:all .15s}
        .back:hover{color:var(--green-dark);border-color:#86efac;background:rgba(255,255,255,.9);box-shadow:var(--shadow)}

        .steps{display:flex;gap:8px;margin-bottom:24px}
        .step{display:flex;align-items:center;gap:8px;padding:8px 16px;border-radius:999px;font-size:13px;font-weight:700;background:rgba(255,255,255,.6);color:var(--muted);border:1px solid var(--line)}
        .step.active{background:linear-gradient(135deg,#009846,#00b855);color:#fff;border-color:transparent;box-shadow:0 2px 8px rgba(0,152,70,.25)}
        .step-num{width:24px;height:24px;border-radius:50%;display:grid;place-items:center;font-size:12px;font-weight:800;background:rgba(0,0,0,.08)}
        .step.active .step-num{background:rgba(255,255,255,.25)}
        .step-line{width:32px;height:2px;background:var(--line);border-radius:1px}

        .card{background:rgba(255,255,255,.85);backdrop-filter:blur(12px);border:1px solid rgba(232,236,233,.6);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow);transition:box-shadow .2s}
        .card:hover{box-shadow:var(--shadow-md)}
        .card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
        .card-head h1{font-size:18px;font-weight:800;letter-spacing:-.02em;display:flex;align-items:center;gap:10px}
        .card-head h1::before{content:'';width:4px;height:20px;border-radius:3px;background:linear-gradient(180deg,#009846,#00b855);flex-shrink:0}
        .count{font-size:12px;font-weight:700;color:var(--muted);background:var(--mint);padding:3px 10px;border-radius:999px}

        .item{display:grid;grid-template-columns:1fr auto;gap:16px;padding:16px 0;border-bottom:1px solid rgba(232,236,233,.5);align-items:center}
        .item:last-of-type{border-bottom:0}
        .item-info{min-width:0}
        .name{font-weight:700;font-size:15px;margin-bottom:4px}
        .muted{font-size:12px;color:var(--muted)}
        .subtotal{font-weight:800;color:var(--green-dark);margin-top:6px;font-size:14px;font-variant-numeric:tabular-nums}
        .qty{display:flex;align-items:center;gap:4px}
        .qty input{width:58px;border:1.5px solid var(--line);border-radius:8px;padding:8px 4px;font:inherit;font-size:14px;font-weight:600;text-align:center;background:rgba(255,255,255,.8);transition:all .15s}
        .qty input:focus{outline:none;border-color:var(--green);box-shadow:0 0 0 3px rgba(0,152,70,.12);background:#fff}
        .qty-unit{font-size:11px;color:var(--muted);font-weight:600;min-width:24px}

        .total-bar{display:flex;justify-content:space-between;align-items:center;padding:18px 0 0;margin-top:8px;border-top:2px solid var(--ink)}
        .total-bar span{font-size:13px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
        .total-bar strong{font-size:22px;font-weight:900;letter-spacing:-.02em;font-variant-numeric:tabular-nums;background:linear-gradient(135deg,var(--green-dark),#00b855);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

        .btn{width:100%;border:0;border-radius:var(--radius-sm);padding:14px;font:inherit;font-weight:700;font-size:14px;cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none}
        .btn-primary{background:linear-gradient(135deg,#009846,#00b855);color:#fff;box-shadow:0 4px 14px rgba(0,152,70,.3)}
        .btn-primary:hover{box-shadow:0 6px 20px rgba(0,152,70,.4);transform:translateY(-1px)}
        .btn-primary:active{transform:translateY(0)}
        .btn-outline{background:rgba(255,255,255,.8);color:var(--ink);border:1.5px solid var(--line);margin-top:10px}
        .btn-outline:hover{background:#fff;border-color:#a3b1aa;box-shadow:var(--shadow)}

        .empty{text-align:center;padding:48px 20px;color:var(--muted)}
        .empty-icon{font-size:48px;margin-bottom:12px;opacity:.6}
        .empty p{font-size:15px;margin-bottom:8px}
        .empty a{color:var(--green-dark);font-weight:700}

        .bottom{position:fixed;z-index:5;left:0;bottom:0;display:flex;justify-content:space-around;align-items:center;width:100%;height:65px;background:rgba(255,255,255,.88);backdrop-filter:blur(12px);border-top:1px solid var(--line)}
        .bottom a,.bottom button{display:flex;flex-direction:column;align-items:center;gap:2px;font-size:11px;color:var(--muted);text-decoration:none;border:0;background:0;font:inherit;cursor:pointer;padding:6px 12px;font-weight:600}
        .bottom a.active,.bottom button.active{color:var(--green)}
        .bottom .cart-float{position:absolute;right:20px;bottom:72px;display:inline-flex;align-items:center;gap:8px;padding:12px 20px;border-radius:999px;background:linear-gradient(135deg,#009846,#00b855);color:#fff;font-weight:800;font-size:13px;box-shadow:0 4px 16px rgba(0,152,70,.4);text-decoration:none;transition:all .15s}
        .bottom .cart-float:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,152,70,.5)}

        @media(max-width:860px){
            .shell{padding:12px 16px 110px}
            .item{grid-template-columns:1fr 80px}
            .head-inner{padding:16px 16px 36px}
            .steps{flex-wrap:wrap}
        }
    </style>
</head>
<body>
    <header class="head">
        <div class="head-inner">
            <div class="hello">Halo, <b>{{ session('shop_user.name') }}</b><br><small>Keranjang belanja Anda</small></div>
            <div class="avatar">👤</div>
        </div>
    </header>
    <main class="shell">
        @if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif

        <a class="back" href="{{ route('shop.index') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
            Lanjut belanja
        </a>

        <div class="steps">
            <div class="step active"><span class="step-num">1</span> Keranjang</div>
            <div class="step-line"></div>
            <div class="step"><span class="step-num">2</span> Checkout</div>
            <div class="step-line"></div>
            <div class="step"><span class="step-num">3</span> Pembayaran</div>
        </div>

        <div class="card">
            <div class="card-head">
                <h1>Keranjang belanja</h1>
                <span class="count">{{ $cart['total_quantity'] }} item</span>
            </div>
            @if($cart['items'] === [])
                <div class="empty">
                    <div class="empty-icon">🛒</div>
                    <p>Keranjang masih kosong</p>
                    <a href="{{ route('shop.index') }}">Pilih produk terlebih dahulu →</a>
                </div>
            @else
                <form method="post" action="{{ route('shop.cart.update') }}">
                    @csrf @method('patch')
                    @foreach($cart['items'] as $item)
                        <div class="item">
                            <div class="item-info">
                                <div class="name">{{ $item['fruit']->name }}</div>
                                <div class="muted">Rp {{ number_format((float) $item['fruit']->selling_price, 0, ',', '.') }} / {{ $item['fruit']->unit }}</div>
                                <div class="subtotal">Rp {{ number_format($item['line_total'], 0, ',', '.') }}</div>
                            </div>
                            <div class="qty">
                                <input type="number" name="items[{{ $item['fruit']->id }}]" min="0" max="{{ $item['fruit']->current_stock }}" step="0.01" value="{{ $item['quantity'] }}">
                                <span class="qty-unit">{{ $item['fruit']->unit }}</span>
                            </div>
                        </div>
                    @endforeach
                    <div class="total-bar">
                        <span>Total</span>
                        <strong>Rp {{ number_format($cart['subtotal'], 0, ',', '.') }}</strong>
                    </div>
                    <button class="btn btn-outline" type="submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        Update keranjang
                    </button>
                </form>
                <a class="btn btn-primary" href="{{ route('shop.checkout.page') }}" style="margin-top:12px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    Lanjut ke checkout
                </a>
            @endif
        </div>
    </main>
    <nav class="bottom">
        <a href="{{ route('shop.index') }}">⌂<br>Beranda</a>
        <a class="active" href="{{ route('shop.cart') }}">🛒<br>Keranjang</a>
        <form method="post" action="{{ route('shop.logout') }}">@csrf<button type="submit">⇥<br>Keluar</button></form>
        <a class="cart-float" href="{{ route('shop.cart') }}">Rp {{ number_format($cart['subtotal'],0,',','.') }} &nbsp; 🛒</a>
    </nav>
</body>
</html>

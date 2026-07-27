<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout | Cahaya Baru</title>
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

        .shell{max-width:900px;margin:0 auto;padding:12px 20px 60px}
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
        .step.done{background:var(--green-light);color:var(--green-dark);border-color:#86efac}
        .step-num{width:24px;height:24px;border-radius:50%;display:grid;place-items:center;font-size:12px;font-weight:800;background:rgba(0,0,0,.08)}
        .step.active .step-num{background:rgba(255,255,255,.25)}
        .step.done .step-num{background:var(--green);color:#fff}
        .step-line{width:32px;height:2px;background:var(--line);border-radius:1px}
        .step.active + .step-line,.step.done + .step-line{background:var(--green)}

        .grid{display:grid;grid-template-columns:1fr 340px;gap:22px;align-items:start}

        .card{background:rgba(255,255,255,.85);backdrop-filter:blur(12px);border:1px solid rgba(232,236,233,.6);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow);transition:box-shadow .2s}
        .card:hover{box-shadow:var(--shadow-md)}
        .card h2{font-size:16px;font-weight:800;letter-spacing:-.01em;display:flex;align-items:center;gap:10px;margin-bottom:18px}
        .card h2::before{content:'';width:4px;height:20px;border-radius:3px;background:linear-gradient(180deg,#009846,#00b855);flex-shrink:0}

        .field{margin:14px 0}
        .field label{display:block;font-size:11px;font-weight:800;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.06em}
        .field input,.field textarea,.field select{width:100%;border:1.5px solid var(--line);border-radius:var(--radius-sm);padding:12px 14px;font:inherit;font-size:14px;background:rgba(255,255,255,.8);transition:all .15s}
        .field input:focus,.field textarea:focus,.field select:focus{outline:none;border-color:var(--green);box-shadow:0 0 0 3px rgba(0,152,70,.12);background:#fff}
        .field textarea{min-height:64px;resize:vertical}

        .summary-item{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid rgba(232,236,233,.5)}
        .summary-item:last-of-type{border-bottom:0}
        .summary-name{font-weight:600;font-size:14px}
        .summary-detail{font-size:12px;color:var(--muted);margin-top:2px}
        .summary-price{font-weight:700;font-size:14px;font-variant-numeric:tabular-nums;white-space:nowrap}

        .total-bar{display:flex;justify-content:space-between;align-items:center;padding:16px 0 0;margin-top:8px;border-top:2px solid var(--ink)}
        .total-bar span{font-size:13px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
        .total-bar strong{font-size:22px;font-weight:900;letter-spacing:-.02em;font-variant-numeric:tabular-nums;background:linear-gradient(135deg,var(--green-dark),#00b855);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

        .btn{width:100%;border:0;border-radius:var(--radius-sm);padding:14px;font:inherit;font-weight:700;font-size:14px;cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:8px}
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

        @media(max-width:860px){
            .grid{grid-template-columns:1fr}
            .shell{padding:12px 16px 80px}
            .head-inner{padding:16px 16px 36px}
            .steps{flex-wrap:wrap}
        }
    </style>
</head>
<body>
    <header class="head">
        <div class="head-inner">
            <div class="hello">Halo, <b>{{ session('shop_user.name') }}</b><br><small>Selesaikan pesanan Anda</small></div>
            <div class="avatar">👤</div>
        </div>
    </header>
    <main class="shell">
        @if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif

        <a class="back" href="{{ route('shop.cart') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
            Kembali ke keranjang
        </a>

        <div class="steps">
            <div class="step done"><span class="step-num">✓</span> Keranjang</div>
            <div class="step-line"></div>
            <div class="step active"><span class="step-num">2</span> Checkout</div>
            <div class="step-line"></div>
            <div class="step"><span class="step-num">3</span> Pembayaran</div>
        </div>

        <div class="grid">
            <section class="card">
                <h2>Informasi pengiriman</h2>
                <form method="post" action="{{ route('shop.checkout') }}">
                    @csrf
                    <div class="field"><label>Nama lengkap</label><input name="customer_name" value="{{ old('customer_name', session('shop_user.name')) }}" required></div>
                    <div class="field"><label>No. WhatsApp / Telepon</label><input name="customer_phone" value="{{ old('customer_phone') }}" placeholder="08xxxxxxxxxx" required></div>
                    <div class="field"><label>Email</label><input type="email" name="customer_email" value="{{ old('customer_email', session('shop_user.email')) }}" placeholder="email@contoh.com"></div>
                    <div class="field"><label>Alamat pengantaran</label><textarea name="delivery_address" placeholder="Alamat lengkap pengantaran" required>{{ old('delivery_address') }}</textarea></div>
                    <div class="field"><label>Catatan</label><textarea name="notes" style="min-height:52px;" placeholder="Catatan untuk penjual">{{ old('notes') }}</textarea></div>
                    <div class="field"><label>Metode pembayaran</label><select name="payment_gateway">
                        @if(config('services.doku.client_id'))<option value="doku">DOKU (Semua Metode Pembayaran)</option>@endif
                        @if(config('services.midtrans.server_key'))<option value="midtrans">Midtrans (Virtual Account)</option>@endif
                    </select></div>
                    <button class="btn btn-primary" type="submit" @disabled($cart['items'] === [])>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        Buat pesanan & bayar
                    </button>
                </form>
            </section>

            <aside class="card">
                <h2>Ringkasan pesanan</h2>
                @if($cart['items'] === [])
                    <div class="empty">
                        <div class="empty-icon">🛒</div>
                        <p>Keranjang kosong</p>
                        <a href="{{ route('shop.index') }}">Pilih produk →</a>
                    </div>
                @else
                    @foreach($cart['items'] as $item)
                        <div class="summary-item">
                            <div>
                                <div class="summary-name">{{ $item['fruit']->name }}</div>
                                <div class="summary-detail">{{ number_format((float)$item['quantity'], 2, ',', '.') }} {{ $item['fruit']->unit }} × Rp {{ number_format((float)$item['fruit']->selling_price, 0, ',', '.') }}</div>
                            </div>
                            <div class="summary-price">Rp {{ number_format($item['line_total'], 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                    <div class="total-bar">
                        <span>Total</span>
                        <strong>Rp {{ number_format($cart['subtotal'], 0, ',', '.') }}</strong>
                    </div>
                @endif
            </aside>
        </div>
    </main>
    <nav class="bottom">
        <a href="{{ route('shop.index') }}">⌂<br>Beranda</a>
        <a href="{{ route('shop.cart') }}">🛒<br>Keranjang</a>
        <form method="post" action="{{ route('shop.logout') }}">@csrf<button type="submit">⇥<br>Keluar</button></form>
    </nav>
</body>
</html>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cahaya Baru Mart</title>
    <style>
        :root{--green:#009846;--green-dark:#007237;--mint:#e9f8ef;--ink:#1d2420;--muted:#7b827e;--line:#e8ece9;--bg:#f5f7f5}*{box-sizing:border-box}body{margin:0;font:14px Arial,sans-serif;color:var(--ink);background:var(--bg)}a{color:inherit;text-decoration:none}
        .head{background:var(--green);color:#fff;padding:18px 20px 38px}
        .head-inner{max-width:1200px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;gap:14px}
        .hello b{font-size:15px}.hello small{opacity:.8}
        .avatar{width:34px;height:34px;border:2px solid #fff;border-radius:50%;display:grid;place-items:center}
        .top-action{max-width:1200px;margin:0 auto;padding:16px 20px 0;display:flex;justify-content:flex-end}
        .cart-button{position:relative;display:grid;place-items:center;width:48px;border-radius:8px;background:#fff;color:var(--green);font-size:22px;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,.1);transition:transform .15s}
        .cart-button:hover{transform:scale(1.08)}
        .badge{position:absolute;right:-4px;top:-4px;min-width:18px;height:18px;padding:2px 5px;border-radius:20px;background:#ffca28;color:#173220;font-size:11px;font-weight:bold;text-align:center}
        .shell{max-width:1200px;margin:0 auto;padding:12px 20px 100px}
        .notice,.errors{padding:12px;border-radius:8px;margin-bottom:13px}.notice{background:#dff5e8;color:#17643a}.errors{background:#fff0f0;color:#a42e2e}
        .promo{padding:32px;border-radius:12px;overflow:hidden;color:#fff;background:linear-gradient(130deg,#00b855,#00843c);position:relative}
        .promo h1{position:relative;z-index:1;margin:0;font-size:32px;line-height:1.05}.promo p{position:relative;z-index:1;margin:9px 0;font-weight:bold}
        .section-title{margin:28px 2px 14px;font-size:18px;font-weight:700;display:flex;align-items:center;gap:10px}
        .section-title .count{font-size:12px;font-weight:600;color:var(--muted);background:#fff;padding:2px 10px;border-radius:999px}
        .categories{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
        .category{padding:14px 6px;border-radius:10px;text-align:center;background:#fff;color:var(--muted);font-size:12px;font-weight:600;box-shadow:0 1px 2px #0000000d;cursor:pointer;transition:box-shadow .15s}
        .category:hover{box-shadow:0 2px 8px rgba(0,0,0,.1)}
        .category span{display:block;font-size:24px;margin-bottom:4px}
        .products{display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:14px}
        .product{display:grid;grid-template-columns:auto 1fr;background:#fff;border:1px solid var(--line);border-radius:12px;overflow:hidden;transition:box-shadow .15s}
        .product:hover{box-shadow:0 2px 8px rgba(0,0,0,.08)}
        .art{width:88px;display:grid;place-items:center;font-size:36px;background:var(--mint)}
        .product-body{padding:14px;display:grid;gap:8px}
        .product-name{font-size:15px;font-weight:700}.meta{font-size:12px;color:var(--muted)}
        .price{font-size:18px;font-weight:800;font-variant-numeric:tabular-nums}.price small{font-size:11px;font-weight:500;color:var(--muted)}
        .add-form{display:flex;gap:6px;align-items:center}
        .add-form input{width:60px;border:1px solid var(--line);border-radius:6px;padding:7px 6px;font:inherit;font-size:13px;text-align:center}
        .add{border:0;border-radius:6px;padding:8px 14px;background:var(--green);color:#fff;font:inherit;font-weight:700;font-size:13px;cursor:pointer;white-space:nowrap;transition:background .15s}
        .add:hover{background:var(--green-dark)}
        .bottom{position:fixed;z-index:5;left:0;bottom:0;display:flex;justify-content:space-around;align-items:center;width:100%;height:65px;background:#fff;border-top:1px solid var(--line)}
        .bottom a,.bottom button{display:flex;flex-direction:column;align-items:center;gap:2px;font-size:11px;color:var(--muted);text-decoration:none;border:0;background:0;font:inherit;cursor:pointer;padding:6px 12px}
        .bottom a.active,.bottom button.active{color:var(--green)}
        .bottom .cart{position:absolute;right:20px;bottom:72px;display:inline-flex;align-items:center;gap:6px;padding:10px 18px;border-radius:999px;background:var(--green);color:#fff;font-weight:700;font-size:13px;box-shadow:0 4px 12px rgba(0,152,70,.35);text-decoration:none;transition:transform .15s}
        .bottom .cart:hover{transform:translateY(-2px)}
        @media(max-width:720px){.categories{grid-template-columns:repeat(2,1fr)}.products{grid-template-columns:1fr}.promo h1{font-size:24px}.promo{padding:24px 18px}.shell{padding:12px 16px 100px}.top-action{padding:12px 16px 0}}
    </style>
</head>
<body>
    <header class="head">
        <div class="head-inner">
            <div class="hello">Halo, <b>{{ session('shop_user.name') }}</b><br><small>Buah segar untuk hari ini</small></div>
            <div class="avatar">👤</div>
        </div>
    </header>
    <div class="top-action">
        <a class="cart-button" href="{{ route('shop.cart') }}">🛒@if($cart['total_quantity']>0)<span class="badge">{{ (int)$cart['total_quantity'] }}</span>@endif</a>
    </div>
    <main class="shell">
        @if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
        <section class="promo"><h1>Buah Segar<br>Setiap Hari</h1><p>Diskon hingga 20% untuk pilihan hari ini</p></section>
        <h2 class="section-title">Kategori <span class="count">4 kategori</span></h2>
        <section class="categories">
            <div class="category"><span>🍌</span>Pisang</div>
            <div class="category"><span>🍊</span>Jeruk</div>
            <div class="category"><span>🥭</span>Mangga</div>
            <div class="category"><span>🍎</span>Buah lain</div>
        </section>
        <h2 class="section-title">Produk segar <span class="count">{{ $products->count() }} produk</span></h2>
        <section class="products">
            @php($emoji=['🍌','🍎','🥭','🍊'])
            @forelse($products as $idx => $product)
                <article class="product">
                    <div class="art">{{ $emoji[$idx % count($emoji)] }}</div>
                    <div class="product-body">
                        <div class="product-name">{{ $product->name }}</div>
                        <div class="meta">Stok {{ number_format((float)$product->current_stock, 0, ',', '.') }} {{ $product->unit }}</div>
                        <div class="price">Rp {{ number_format((float)$product->selling_price, 0, ',', '.') }} <small>/ {{ $product->unit }}</small></div>
                        <form class="add-form" method="post" action="{{ route('shop.cart.add',$product) }}">@csrf<input name="quantity" type="number" min="0.01" max="{{ $product->current_stock }}" step="0.01" value="1"><button class="add" type="submit">+ Tambah</button></form>
                    </div>
                </article>
            @empty <p>Belum ada produk tersedia.</p>
            @endforelse
        </section>
    </main>
    <nav class="bottom">
        <a class="active" href="{{ route('shop.index') }}">⌂<br>Beranda</a>
        <a href="{{ route('shop.cart') }}">🛒<br>Keranjang</a>
        <form method="post" action="{{ route('shop.logout') }}">@csrf<button type="submit">⇥<br>Keluar</button></form>
        <a class="cart" href="{{ route('shop.cart') }}">Rp {{ number_format($cart['subtotal'],0,',','.') }} &nbsp; 🛒</a>
    </nav>
</body>
</html>

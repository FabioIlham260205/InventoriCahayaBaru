<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran {{ $order->order_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #0f172a; --muted: #64748b; --line: #e2e8f0;
            --panel: #ffffff; --bg: #f8fafc;
            --green: #16a34a; --green-light: #dcfce7; --green-dark: #15803d;
            --red: #dc2626; --red-light: #fee2e2;
            --yellow: #ca8a04; --yellow-light: #fef9c3;
            --blue: #2563eb; --blue-light: #dbeafe;
            --radius: 12px; --radius-sm: 8px;
            --shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,.07), 0 2px 4px -2px rgba(0,0,0,.05);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.08), 0 4px 6px -4px rgba(0,0,0,.04);
        }
        * { box-sizing: border-box; margin: 0; }
        body { font-family:'Inter',system-ui,-apple-system,sans-serif; color:var(--ink); background:var(--bg); -webkit-font-smoothing:antialiased; }
        a { color:inherit; text-decoration:none; }

        .shell { max-width:1200px; margin:0 auto; padding:32px 20px 60px; }

        .topbar { background:var(--panel); border-bottom:1px solid var(--line); }
        .topbar-inner { max-width:1200px; margin:0 auto; padding:16px 20px; display:flex; align-items:center; justify-content:space-between; gap:14px; }
        .brand { display:flex; align-items:center; gap:12px; font-weight:800; font-size:17px; }
        .brand-icon { width:40px; height:40px; border-radius:var(--radius-sm); display:grid; place-items:center; background:linear-gradient(135deg,#16a34a,#22c55e); color:#fff; font-size:16px; font-weight:900; box-shadow:0 2px 8px rgba(22,163,74,.3); }
        .back-link { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:var(--radius-sm); font-weight:600; font-size:13px; color:var(--muted); border:1px solid var(--line); background:var(--panel); transition:all .15s ease; }
        .back-link:hover { color:var(--ink); border-color:#cbd5e1; box-shadow:var(--shadow); }

        .notice { margin-bottom:20px; border:1px solid #bbf7d0; color:#166534; background:linear-gradient(135deg,#f0fdf4,#dcfce7); border-radius:var(--radius); padding:14px 16px; font-weight:500; display:flex; align-items:center; gap:10px; }
        .notice::before { content:''; width:8px; height:8px; border-radius:50%; background:#16a34a; flex-shrink:0; }

        .page-header { margin-bottom:28px; }
        .page-header h1 { font-size:28px; font-weight:800; letter-spacing:-.02em; margin-bottom:6px; }
        .page-header p { color:var(--muted); font-size:14px; }

        .grid { display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start; }

        .card { background:var(--panel); border:1px solid var(--line); border-radius:var(--radius); padding:20px; box-shadow:var(--shadow); }
        .card h2 { font-size:15px; font-weight:700; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .card h2::before { content:''; width:4px; height:18px; border-radius:2px; background:var(--green); flex-shrink:0; }

        table { width:100%; border-collapse:collapse; }
        th { text-align:left; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; padding:0 12px 10px; }
        td { padding:12px; font-size:14px; border-bottom:1px solid #f1f5f9; }
        tr:last-child td { border-bottom:none; }
        td:last-child { text-align:right; font-weight:600; font-variant-numeric:tabular-nums; }

        .summary-card { display:grid; gap:0; overflow:hidden; }

        .badge { display:inline-flex; align-items:center; gap:5px; border-radius:999px; padding:6px 12px; font-size:12px; font-weight:700; letter-spacing:.01em; }
        .badge::before { content:''; width:7px; height:7px; border-radius:50%; flex-shrink:0; }
        .badge.paid { color:#166534; background:#dcfce7; }
        .badge.paid::before { background:#16a34a; }
        .badge.waiting { color:#854d0e; background:#fef9c3; }
        .badge.waiting::before { background:#ca8a04; animation:pulse 2s infinite; }
        .badge.failed { color:#991b1b; background:#fee2e2; }
        .badge.failed::before { background:#dc2626; }
        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:.4; } }

        .summary-rows { padding:16px 20px; display:grid; gap:12px; border-bottom:1px solid var(--line); }
        .summary-row { display:flex; justify-content:space-between; align-items:center; font-size:14px; }
        .summary-row span:first-child { color:var(--muted); }
        .summary-row strong { font-weight:600; font-variant-numeric:tabular-nums; }

        .total-row { padding:16px 20px; display:flex; justify-content:space-between; align-items:baseline; }
        .total-label { font-size:14px; font-weight:600; color:var(--muted); }
        .total-value { font-size:26px; font-weight:800; letter-spacing:-.02em; font-variant-numeric:tabular-nums; }

        .action-area { padding:20px; display:grid; gap:12px; }

        .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; border:none; border-radius:var(--radius-sm); padding:12px 20px; font:inherit; font-weight:700; font-size:14px; cursor:pointer; transition:all .15s ease; text-decoration:none; }
        .btn-primary { color:#fff; background:var(--green); box-shadow:0 2px 8px rgba(22,163,74,.25); }
        .btn-primary:hover { background:var(--green-dark); box-shadow:0 4px 12px rgba(22,163,74,.35); transform:translateY(-1px); }
        .btn-secondary { color:var(--ink); background:var(--panel); border:1px solid var(--line); }
        .btn-secondary:hover { background:#f8fafc; border-color:#cbd5e1; box-shadow:var(--shadow); }

        .paid-box { display:grid; gap:8px; padding:16px; border-radius:var(--radius-sm); background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1px solid #bbf7d0; }
        .paid-box .icon { width:40px; height:40px; border-radius:50%; display:grid; place-items:center; background:#16a34a; color:#fff; font-size:20px; margin-bottom:4px; }
        .paid-box strong { color:#166534; font-size:15px; }
        .paid-box p { color:#166534; font-size:13px; opacity:.8; }

        .error-box { display:grid; gap:12px; padding:16px; border-radius:var(--radius-sm); background:#fefce8; border:1px solid #fde68a; }
        .error-box .icon { width:40px; height:40px; border-radius:50%; display:grid; place-items:center; background:#ca8a04; color:#fff; font-size:18px; margin-bottom:4px; }
        .error-box strong { color:#854d0e; font-size:15px; }
        .error-box p { color:#854d0e; font-size:13px; opacity:.8; }
        .error-actions { display:grid; gap:8px; margin-top:4px; }
        .btn-retry { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 16px; border-radius:var(--radius-sm); font-weight:700; font-size:13px; border:1px solid #d4a017; background:#fff; color:#854d0e; transition:all .15s ease; }
        .btn-retry:hover { background:#fef9c3; border-color:#ca8a04; box-shadow:var(--shadow); transform:translateY(-1px); }
        .no-gateway { color:#991b1b; font-size:13px; font-weight:500; }

        .footer-note { margin-top:16px; text-align:center; font-size:12px; color:var(--muted); }

        @media (max-width:768px) {
            .grid { grid-template-columns:1fr; }
            .topbar-inner { padding:12px 16px; }
            .shell { padding:20px 16px 40px; }
            .page-header h1 { font-size:22px; }
            .total-value { font-size:22px; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="{{ route('shop.index') }}">
                <span class="brand-icon">CB</span>
                <span>Cahaya Baru</span>
            </a>
            <a class="back-link" href="{{ route('shop.index') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                Kembali ke toko
            </a>
        </div>
    </header>

    <main class="shell">
        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif

        <div class="page-header">
            <h1>Pembayaran pesanan</h1>
            <p>Nomor pesanan <strong style="color:var(--ink)">{{ $order->order_number }}</strong></p>
        </div>

        <section class="grid">
            <div class="card">
                <h2>Detail belanja</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th style="text-align:center">Jumlah</th>
                            <th style="text-align:right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <div style="font-weight:600">{{ $item->fruit_name }}</div>
                                </td>
                                <td style="text-align:center;color:var(--muted)">
                                    {{ number_format((float) $item->quantity, 2, ',', '.') }} {{ $item->unit }}
                                </td>
                                <td>Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <aside class="card summary-card" style="position:sticky;top:20px;">
                @php
                    $badgeClass = match ($order->payment_status) {
                        'paid' => 'paid',
                        'failed' => 'failed',
                        default => 'waiting',
                    };
                    $statusLabel = match ($order->payment_status) {
                        'paid' => 'Lunas',
                        'failed' => 'Gagal / Kedaluwarsa',
                        default => 'Menunggu pembayaran',
                    };
                @endphp

                <div style="padding:20px 20px 0;">
                    <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                </div>

                <div class="summary-rows">
                    <div class="summary-row"><span>Subtotal</span><strong>Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</strong></div>
                    <div class="summary-row"><span>Ongkir</span><strong>Rp {{ number_format((float) $order->delivery_fee, 0, ',', '.') }}</strong></div>
                </div>

                <div class="total-row">
                    <span class="total-label">Total</span>
                    <span class="total-value">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</span>
                </div>

                <div class="action-area">
                    @if ($order->payment_status === 'paid')
                        <div class="paid-box">
                            <div class="icon">&#10003;</div>
                            <strong>Pembayaran diterima</strong>
                            <p>Tim Cahaya Baru akan menyiapkan pesanan Anda.</p>
                        </div>
                    @elseif (in_array($order->payment_provider, ['midtrans', 'doku']) && $order->payment_redirect_url)
                        <a class="btn btn-primary" href="{{ $order->payment_redirect_url }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                            Bayar sekarang
                        </a>
                        <p style="text-align:center;font-size:12px;color:var(--muted);margin:0;">Anda akan diarahkan ke {{ ucfirst($order->payment_provider) }}.</p>
                    @else
                        <div class="error-box">
                            <div class="icon">&#9888;</div>
                            <strong>Gagal terhubung ke payment gateway</strong>
                            <p>Pilih metode pembayaran lain atau hubungi admin Cahaya Baru.</p>
                            <div class="error-actions">
                                @if (config('services.doku.client_id'))
                                    <a class="btn-retry" href="{{ route('shop.payment.show', ['order' => $order, 'gateway' => 'doku']) }}">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                        Coba DOKU (Semua Metode)
                                    </a>
                                @endif
                                @if (config('services.midtrans.server_key'))
                                    <a class="btn-retry" href="{{ route('shop.payment.show', ['order' => $order, 'gateway' => 'midtrans']) }}">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                        Coba Midtrans (VA)
                                    </a>
                                @endif
                                @if (! config('services.midtrans.server_key') && ! config('services.doku.client_id'))
                                    <p class="no-gateway">Belum ada payment gateway yang dikonfigurasi. Hubungi admin.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </aside>
        </section>

        <p class="footer-note">Pesanan {{ $order->order_number }} &middot; Cahaya Baru</p>
    </main>
</body>
</html>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Inventory Buah Cahaya Baru' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --green:#009846;--green-light:#dcfce7;--green-dark:#007237;
            --mint:#e9f8ef;--ink:#1d2420;--muted:#7b827e;--line:#e8ece9;
            --bg:linear-gradient(160deg,#e9f8ef 0%,#f5f7f5 40%,#f0faf4 100%);
            --card:#ffffff;--radius:14px;--radius-sm:10px;
            --shadow:0 2px 8px rgba(0,0,0,.06);--shadow-md:0 8px 24px rgba(0,0,0,.08);
            --font:'Inter',system-ui,-apple-system,sans-serif;
            --red:#dc2626;--yellow:#ca8a04;--blue:#2563eb;
        }
        *{box-sizing:border-box}
        body{margin:0;font-family:var(--font);color:var(--ink);background:var(--bg);min-height:100vh;-webkit-font-smoothing:antialiased}
        a{color:inherit;text-decoration:none}

        .app-frame{min-height:100vh;display:grid;grid-template-columns:260px minmax(0,1fr);transition:grid-template-columns .2s ease}
        .app-frame.sidebar-collapsed{grid-template-columns:72px minmax(0,1fr)}

        .sidebar{position:sticky;top:0;height:100vh;padding:20px 14px;background:linear-gradient(180deg,#0a1f14 0%,#0d2a1a 50%,#071a0f 100%);color:#f8f4f8;border-right:1px solid rgba(0,152,70,.15);display:flex;flex-direction:column;gap:20px;overflow-y:auto}
        .sidebar-toggle{position:absolute;top:28px;right:-16px;width:32px;height:32px;border-radius:999px;border:1px solid rgba(0,152,70,.3);background:linear-gradient(135deg,#0d2a1a,#0a1f14);color:#4ade80;display:grid;place-items:center;cursor:pointer;z-index:5;transition:all .15s}
        .sidebar-toggle:hover{background:linear-gradient(135deg,#166534,#0d2a1a);box-shadow:0 0 12px rgba(0,152,70,.3)}
        .sidebar-toggle svg{width:16px;height:16px;transition:transform .2s ease}
        .sidebar-collapsed .sidebar-toggle svg{transform:rotate(180deg)}

        .brand{display:flex;align-items:center;gap:10px;padding:4px 10px;font-weight:800;font-size:16px;letter-spacing:-.02em}
        .brand-icon{width:40px;height:40px;border-radius:10px;display:grid;place-items:center;background:linear-gradient(135deg,#009846,#00b855);color:#fff;font-size:14px;font-weight:900;box-shadow:0 4px 12px rgba(0,152,70,.35);flex:0 0 auto}
        .brand-text{overflow:hidden;white-space:nowrap}

        .profile-panel{display:flex;align-items:center;gap:12px;padding:16px 10px;border-top:1px solid rgba(255,255,255,.08);border-bottom:1px solid rgba(255,255,255,.08);position:relative}
        .profile-panel::after{content:"";position:absolute;right:14px;top:20px;width:8px;height:8px;border-radius:999px;background:#4ade80;box-shadow:0 0 12px #4ade80}
        .profile-photo{width:48px;height:48px;border-radius:999px;object-fit:cover;flex:0 0 auto;border:2px solid rgba(0,152,70,.3);background:linear-gradient(135deg,#166534,#0d2a1a)}
        .profile-fallback{display:grid;place-items:center;color:#fff;font-size:16px;font-weight:800;background:linear-gradient(135deg,#009846,#00b855);border-radius:999px;width:48px;height:48px}
        .profile-meta{min-width:0;display:grid;gap:2px}
        .profile-role{color:#4ade80;font-size:10px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
        .profile-name{color:#fff;font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:14px}
        .profile-email{color:rgba(255,255,255,.6);font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

        .sidebar-section{padding:0 10px;color:rgba(255,255,255,.5);font-size:10px;font-weight:800;letter-spacing:.2em;text-transform:uppercase;margin-top:4px}
        .links{display:grid;gap:6px}
        .nav-link{min-height:42px;display:flex;align-items:center;gap:12px;border-radius:8px;padding:0 12px;color:rgba(255,255,255,.6);font-weight:600;font-size:13px;transition:all .15s}
        .nav-link:hover{color:#fff;background:rgba(255,255,255,.06)}
        .nav-link svg{width:18px;height:18px;color:rgba(255,255,255,.4);flex:0 0 auto}
        .nav-link:hover svg{color:rgba(255,255,255,.7)}
        .nav-link.active{color:#fff;background:linear-gradient(135deg,#009846,#00b855);box-shadow:0 2px 8px rgba(0,152,70,.3)}
        .nav-link.active svg{color:#fff}

        .sidebar-footer{margin-top:auto;display:grid;gap:8px;padding:0 8px}
        .user-email{color:rgba(255,255,255,.4);font-size:11px;line-height:1.4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .logout-form{margin:0}
        .logout-btn{width:100%;display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:8px;border:1px solid rgba(255,255,255,.1);background:rgba(255,255,255,.04);color:rgba(255,255,255,.6);font:inherit;font-weight:600;font-size:13px;cursor:pointer;transition:all .15s}
        .logout-btn:hover{background:rgba(220,38,38,.15);border-color:rgba(220,38,38,.3);color:#fca5a5}

        .sidebar-collapsed .brand{justify-content:center;padding-left:0;padding-right:0}
        .sidebar-collapsed .brand-text,.sidebar-collapsed .profile-meta,.sidebar-collapsed .profile-panel::after,.sidebar-collapsed .nav-link span,.sidebar-collapsed .sidebar-section,.sidebar-collapsed .user-email,.sidebar-collapsed .logout-btn span{display:none}
        .sidebar-collapsed .profile-panel{justify-content:center;padding-left:0;padding-right:0}
        .sidebar-collapsed .profile-photo,.sidebar-collapsed .profile-fallback{width:40px;height:40px}
        .sidebar-collapsed .nav-link{justify-content:center;padding:0}
        .sidebar-collapsed .logout-btn{justify-content:center;padding:10px}

        .content-wrap{min-width:0}
        .shell{max-width:1200px;margin:0 auto;padding:24px}

        .header{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;margin:0 0 24px}
        .header h1{margin:0 0 4px;font-size:26px;font-weight:800;letter-spacing:-.02em}
        .header p{margin:0;color:var(--muted);font-size:14px}
        h1{margin:0 0 6px;font-size:26px;font-weight:800;letter-spacing:-.02em}
        h2{margin:0 0 16px;font-size:17px;font-weight:800;letter-spacing:-.01em;display:flex;align-items:center;gap:10px}
        h2::before{content:'';width:4px;height:18px;border-radius:3px;background:linear-gradient(180deg,#009846,#00b855);flex-shrink:0}
        p{margin:0;color:var(--muted);line-height:1.5}

        .grid{display:grid;gap:16px}
        .stats{grid-template-columns:repeat(4,minmax(0,1fr))}
        .two{grid-template-columns:minmax(0,1.25fr) minmax(320px,.75fr);align-items:start}

        .card{background:rgba(255,255,255,.85);backdrop-filter:blur(12px);border:1px solid rgba(232,236,233,.6);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);transition:box-shadow .2s}
        .card:hover{box-shadow:var(--shadow-md)}
        .stat{display:grid;gap:4px}
        .stat span{font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.04em}
        .stat strong{font-size:28px;font-weight:900;letter-spacing:-.02em;background:linear-gradient(135deg,var(--green-dark),#00b855);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

        table{width:100%;border-collapse:collapse}
        th{text-align:left;padding:10px;border-bottom:2px solid var(--line);font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;font-weight:700}
        td{padding:10px;border-bottom:1px solid rgba(232,236,233,.5);font-size:14px;vertical-align:top}
        tr:last-child td{border-bottom:0}

        label{display:block;font-size:11px;font-weight:800;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.06em}
        input,select,textarea{width:100%;border:1.5px solid var(--line);border-radius:var(--radius-sm);padding:11px 14px;font:inherit;font-size:14px;background:rgba(255,255,255,.8);transition:all .15s}
        input:focus,select:focus,textarea:focus{outline:none;border-color:var(--green);box-shadow:0 0 0 3px rgba(0,152,70,.12);background:#fff}
        textarea{min-height:80px;resize:vertical}

        .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
        .full{grid-column:1/-1}

        .button{display:inline-flex;align-items:center;justify-content:center;gap:6px;border:1.5px solid var(--line);border-radius:var(--radius-sm);padding:10px 16px;background:rgba(255,255,255,.8);cursor:pointer;font:inherit;font-weight:700;font-size:13px;white-space:nowrap;transition:all .15s}
        .button:hover{background:#fff;border-color:#a3b1aa;box-shadow:var(--shadow)}
        .button.primary{color:#fff;background:linear-gradient(135deg,#009846,#00b855);border-color:transparent;box-shadow:0 2px 8px rgba(0,152,70,.25)}
        .button.primary:hover{box-shadow:0 4px 12px rgba(0,152,70,.35);transform:translateY(-1px)}

        .badge{display:inline-flex;border-radius:999px;padding:4px 10px;font-size:11px;font-weight:700;letter-spacing:.01em}
        .badge.ok{color:#166534;background:linear-gradient(135deg,#dcfce7,#bbf7d0);border:1px solid #86efac}
        .badge.warn{color:#854d0e;background:linear-gradient(135deg,#fef9c3,#fde68a);border:1px solid #fcd34d}
        .badge.danger{color:#991b1b;background:linear-gradient(135deg,#fee2e2,#fecaca);border:1px solid #fca5a5}

        .notice{margin-bottom:16px;padding:14px 16px;border-radius:var(--radius-sm);font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,#dcfce7,#bbf7d0);color:#166534;border:1px solid #86efac}
        .notice::before{content:'✓';width:22px;height:22px;border-radius:50%;background:#16a34a;color:#fff;display:grid;place-items:center;font-size:12px;flex-shrink:0}
        .errors{margin-bottom:16px;padding:14px 16px;border-radius:var(--radius-sm);font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,#fee2e2,#fecaca);color:#991b1b;border:1px solid #fca5a5}
        .errors::before{content:'!';width:22px;height:22px;border-radius:50%;background:#dc2626;color:#fff;display:grid;place-items:center;font-size:14px;font-weight:900;flex-shrink:0}

        .stack{display:grid;gap:12px}
        .item{border:1px solid rgba(232,236,233,.6);border-radius:var(--radius-sm);padding:14px;background:rgba(255,255,255,.7);transition:all .15s}
        .item:hover{box-shadow:var(--shadow);background:rgba(255,255,255,.9)}
        .actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
        .money{white-space:nowrap;font-variant-numeric:tabular-nums}

        @media(max-width:860px){
            .app-frame,.app-frame.sidebar-collapsed{grid-template-columns:1fr}
            .sidebar{position:relative;height:auto;padding:14px;gap:14px}
            .sidebar-toggle{display:none}
            .links{grid-template-columns:repeat(2,minmax(0,1fr))}
            .nav-link{font-size:12px}
            .sidebar-footer{margin-top:0}
            .sidebar-collapsed .brand-text,.sidebar-collapsed .profile-meta,.sidebar-collapsed .nav-link span,.sidebar-collapsed .sidebar-section,.sidebar-collapsed .user-email,.sidebar-collapsed .logout-btn span{display:initial}
            .sidebar-collapsed .profile-panel{justify-content:flex-start}
            .sidebar-collapsed .nav-link{justify-content:flex-start;padding:0 12px}
            .stats,.two,.form-grid{grid-template-columns:1fr}
            .header{display:block}
            .shell{padding:16px}
        }
        @media print{.sidebar,.no-print{display:none !important}.app-frame{display:block}body{background:#fff}.shell{max-width:none;padding:0}.card{border:0;padding:0;backdrop-filter:none}}
    </style>
</head>
<body>
    @php
        $inventoryUser = session('inventory_user', []);
        $userName = $inventoryUser['name'] ?? 'Administrator';
        $userEmail = $inventoryUser['email'] ?? 'admin@cahayabaru.local';
        $userPicture = $inventoryUser['picture'] ?? null;
        $initials = collect(explode(' ', trim($userName)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
    @endphp
    <div class="app-frame" id="appFrame">
        <aside class="sidebar no-print">
            <button class="sidebar-toggle" type="button" id="sidebarToggle" aria-label="Buka tutup menu inventory">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"></path></svg>
            </button>
            <a class="brand" href="{{ route('inventory.index') }}">
                <div class="brand-icon">CB</div>
                <span class="brand-text">Inventory</span>
            </a>

            <div class="profile-panel">
                @if ($userPicture)
                    <img class="profile-photo" src="{{ $userPicture }}" alt="Foto profil {{ $userName }}">
                @else
                    <div class="profile-fallback">{{ $initials ?: 'AD' }}</div>
                @endif
                <div class="profile-meta">
                    <span class="profile-role">Administrator</span>
                    <span class="profile-name">{{ $userName }}</span>
                    <span class="profile-email">{{ $userEmail }}</span>
                </div>
            </div>

            <div class="links">
                <div class="sidebar-section">Menu</div>
                <a class="nav-link {{ request()->routeIs('dashboard.detail') ? 'active' : '' }}" href="{{ route('dashboard.detail') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="8" r="3"></circle><path d="M14 6h6"></path><path d="M14 12h6"></path><path d="M4 19h16"></path></svg>
                    <span>Detail</span>
                </a>
                <a class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5h16v14H4z"></path><path d="M8 9h8"></path><path d="M8 13h5"></path></svg>
                    <span>Pencatatan</span>
                </a>
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 19V5"></path><path d="M5 19h14"></path><path d="M9 15l3-4 3 2 4-6"></path></svg>
                    <span>Laporan</span>
                </a>
                <a class="nav-link {{ request()->routeIs('communication.*') ? 'active' : '' }}" href="{{ route('communication.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path><path d="M8 8h8"></path><path d="M8 12h5"></path></svg>
                    <span>Notif & Komunikasi</span>
                </a>
                <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2l1.5 4h9L18 2"></path><path d="M5 6h14l-1 15H6L5 6z"></path><path d="M9 11h6"></path><path d="M10 15h4"></path></svg>
                    <span>Pesanan</span>
                </a>
            </div>

            <div class="sidebar-footer">
                <span class="user-email">{{ $userEmail }}</span>
                <form class="logout-form" method="post" action="{{ route('inventory.logout') }}">
                    @csrf
                    <button class="logout-btn" type="submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 17l5-5-5-5"></path><path d="M15 12H3"></path><path d="M21 3v18"></path></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="content-wrap">
            <main class="shell">
                @if (session('status'))
                    <div class="notice">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="errors">{{ $errors->first() }}</div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
    <script>
        const frame = document.getElementById('appFrame');
        const toggle = document.getElementById('sidebarToggle');
        const saved = localStorage.getItem('inventorySidebarCollapsed');

        if (saved === 'true') {
            frame.classList.add('sidebar-collapsed');
        }

        toggle?.addEventListener('click', () => {
            frame.classList.toggle('sidebar-collapsed');
            localStorage.setItem('inventorySidebarCollapsed', frame.classList.contains('sidebar-collapsed') ? 'true' : 'false');
        });
    </script>
</body>
</html>

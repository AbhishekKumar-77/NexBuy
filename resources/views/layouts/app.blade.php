<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="NexBuy — India's smartest GeM vs marketplace price comparison tool. Compare GeM, Amazon, Flipkart & IndiaMART prices with compliance checks, TCO calculator and fraud detection."/>
    <title>@yield('title', 'NexBuy — GeM Price Comparison Platform')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet"/>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        /* ─── RESET & BASE ─── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary:       #6C63FF;
            --primary-dark:  #574fd6;
            --primary-light: #8b85ff;
            --accent:        #00D4AA;
            --accent-dark:   #00b893;
            --gem:           #f59e0b;
            --gem-dark:      #d97706;
            --amazon:        #FF9900;
            --flipkart:      #2874F0;
            --indiamart:     #e63946;
            --danger:        #ef4444;
            --success:       #22c55e;
            --warning:       #f59e0b;
            --bg:            #0a0a14;
            --bg2:           #12121f;
            --bg3:           #1a1a2e;
            --border:        rgba(255,255,255,0.08);
            --text:          #e8e8f0;
            --text-muted:    #8888a8;
            --glass:         rgba(255,255,255,0.04);
            --glass-border:  rgba(255,255,255,0.10);
            --radius:        14px;
            --radius-sm:     8px;
            --radius-lg:     20px;
            --shadow:        0 8px 32px rgba(0,0,0,0.4);
        }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* ─── SCROLLBAR ─── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg2); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 3px; }

        /* ─── NAVBAR ─── */
        .navbar {
            position: sticky; top: 0; z-index: 1000;
            background: rgba(10,10,20,0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
        }
        .nav-inner {
            max-width: 1400px; margin: 0 auto;
            display: flex; align-items: center; gap: 2rem; height: 64px;
        }
        .nav-logo {
            font-family: 'Outfit', sans-serif; font-size: 1.5rem; font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-decoration: none; white-space: nowrap;
            letter-spacing: -0.5px;
        }
        .nav-logo span { -webkit-text-fill-color: var(--accent); }
        .nav-search-wrap { flex: 1; max-width: 540px; }
        .nav-search {
            width: 100%; display: flex; align-items: center; gap: 0;
            background: var(--glass); border: 1px solid var(--glass-border);
            border-radius: 50px; overflow: hidden;
        }
        .nav-search input {
            flex: 1; border: none; background: none; color: var(--text);
            padding: 0.55rem 1.2rem; font-size: 0.9rem; outline: none;
            font-family: 'Inter', sans-serif;
        }
        .nav-search input::placeholder { color: var(--text-muted); }
        .nav-search button {
            background: var(--primary); border: none; color: white; cursor: pointer;
            padding: 0.55rem 1.2rem; font-size: 0.9rem; display: flex; align-items: center;
            gap: 0.4rem; transition: background 0.2s;
        }
        .nav-search button:hover { background: var(--primary-dark); }
        .nav-links { display: flex; align-items: center; gap: 0.25rem; margin-left: auto; }
        .nav-link {
            color: var(--text-muted); text-decoration: none; padding: 0.4rem 0.85rem;
            border-radius: 8px; font-size: 0.875rem; transition: all 0.2s;
            display: flex; align-items: center; gap: 0.4rem; white-space: nowrap;
        }
        .nav-link:hover, .nav-link.active { color: var(--text); background: var(--glass); }
        .nav-link.nav-cta {
            background: var(--primary); color: white; padding: 0.4rem 1rem;
        }
        .nav-link.nav-cta:hover { background: var(--primary-dark); }

        /* ─── MAIN WRAPPER ─── */
        .main { max-width: 1400px; margin: 0 auto; padding: 2rem; }

        /* ─── CARDS ─── */
        .card {
            background: var(--bg2); border: 1px solid var(--border);
            border-radius: var(--radius); overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        }
        .card:hover { transform: translateY(-2px); box-shadow: var(--shadow); border-color: rgba(108,99,255,0.3); }
        .card-body { padding: 1.25rem; }

        /* ─── BADGES ─── */
        .badge {
            display: inline-flex; align-items: center; gap: 0.3rem;
            padding: 0.2rem 0.65rem; border-radius: 50px;
            font-size: 0.72rem; font-weight: 600; letter-spacing: 0.3px;
        }
        .badge-gem      { background: rgba(245,158,11,0.15); color: var(--gem); border: 1px solid rgba(245,158,11,0.3); }
        .badge-amazon   { background: rgba(255,153,0,0.12); color: var(--amazon); border: 1px solid rgba(255,153,0,0.25); }
        .badge-flipkart { background: rgba(40,116,240,0.12); color: var(--flipkart); border: 1px solid rgba(40,116,240,0.25); }
        .badge-indiamart{ background: rgba(230,57,70,0.12); color: var(--indiamart); border: 1px solid rgba(230,57,70,0.25); }
        .badge-success  { background: rgba(34,197,94,0.12); color: var(--success); border: 1px solid rgba(34,197,94,0.3); }
        .badge-danger   { background: rgba(239,68,68,0.12); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); }
        .badge-purple   { background: rgba(108,99,255,0.15); color: var(--primary-light); border: 1px solid rgba(108,99,255,0.3); }
        .badge-accent   { background: rgba(0,212,170,0.12); color: var(--accent); border: 1px solid rgba(0,212,170,0.3); }

        /* ─── BUTTONS ─── */
        .btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.6rem 1.4rem; border-radius: 50px; font-size: 0.875rem;
            font-weight: 600; cursor: pointer; text-decoration: none;
            border: none; transition: all 0.2s; font-family: 'Inter', sans-serif;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-accent  { background: var(--accent); color: #0a0a14; }
        .btn-accent:hover { background: var(--accent-dark); transform: translateY(-1px); }
        .btn-outline  {
            background: transparent; border: 1px solid var(--border); color: var(--text);
        }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-ghost { background: var(--glass); color: var(--text); border: 1px solid var(--glass-border); }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); }
        .btn-sm { padding: 0.4rem 0.9rem; font-size: 0.8rem; }
        .btn-danger { background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); }
        .btn-danger:hover { background: rgba(239,68,68,0.25); }

        /* ─── FORM ELEMENTS ─── */
        .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
        .form-label { font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .form-input, .form-select {
            background: var(--bg3); border: 1px solid var(--border); color: var(--text);
            border-radius: var(--radius-sm); padding: 0.65rem 1rem; font-size: 0.9rem;
            font-family: 'Inter', sans-serif; outline: none; width: 100%;
            transition: border-color 0.2s;
        }
        .form-input:focus, .form-select:focus { border-color: var(--primary); }
        .form-select option { background: var(--bg3); }

        /* ─── PLATFORM COLORS ─── */
        .platform-gem      { color: var(--gem); }
        .platform-amazon   { color: var(--amazon); }
        .platform-flipkart { color: var(--flipkart); }
        .platform-indiamart{ color: var(--indiamart); }

        /* ─── PRICE ─── */
        .price-big { font-size: 1.6rem; font-weight: 800; font-family: 'Outfit', sans-serif; }
        .price-med { font-size: 1.1rem; font-weight: 700; }
        .price-tag { font-size: 0.85rem; color: var(--text-muted); }

        /* ─── TABLES ─── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: var(--bg3); color: var(--text-muted); font-size: 0.78rem;
            font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
            padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); text-align: left;
        }
        tbody td { padding: 0.85rem 1rem; border-bottom: 1px solid var(--border); font-size: 0.9rem; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: var(--glass); }

        /* ─── ALERTS ─── */
        .alert { padding: 1rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1rem; font-size: 0.9rem; }
        .alert-success { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: var(--success); }
        .alert-danger  { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: var(--danger); }

        /* ─── GRID UTILITIES ─── */
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }
        .grid-auto { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem; }
        @media (max-width: 900px) {
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
            .nav-search-wrap { display: none; }
            .main { padding: 1rem; }
        }

        /* ─── UTILITIES ─── */
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-1 { gap: 0.5rem; }
        .gap-2 { gap: 1rem; }
        .mb-1 { margin-bottom: 0.5rem; }
        .mb-2 { margin-bottom: 1rem; }
        .mb-3 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 2rem; }
        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }
        .text-muted { color: var(--text-muted); }
        .text-sm { font-size: 0.85rem; }
        .text-xs { font-size: 0.75rem; }
        .text-lg { font-size: 1.1rem; }
        .font-bold { font-weight: 700; }
        .font-semibold { font-weight: 600; }
        .w-full { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* ─── SECTION HEADINGS ─── */
        .section-title {
            font-family: 'Outfit', sans-serif; font-size: 1.6rem; font-weight: 700;
            margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;
        }
        .section-title .icon {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .page-title {
            font-family: 'Outfit', sans-serif; font-size: 2rem; font-weight: 800;
            margin-bottom: 0.5rem;
        }

        /* ─── SCORE RING ─── */
        .score-ring {
            width: 64px; height: 64px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 800; font-family: 'Outfit', sans-serif;
            position: relative;
        }
        .score-ring.excellent { background: conic-gradient(var(--success) 0% 85%, var(--bg3) 85% 100%); }
        .score-ring.good      { background: conic-gradient(var(--accent) 0% 70%, var(--bg3) 70% 100%); }
        .score-ring.fair      { background: conic-gradient(var(--warning) 0% 50%, var(--bg3) 50% 100%); }
        .score-ring.poor      { background: conic-gradient(var(--danger) 0% 35%, var(--bg3) 35% 100%); }
        .score-inner {
            width: 48px; height: 48px; background: var(--bg2);
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; font-size: 0.8rem; font-weight: 800;
        }

        /* ─── PLATFORM COMPARISON ROW ─── */
        .platform-row {
            display: flex; align-items: center; gap: 1rem;
            padding: 1rem 1.25rem; border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }
        .platform-row:last-child { border-bottom: none; }
        .platform-row:hover { background: var(--glass); }
        .platform-logo {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 800; flex-shrink: 0;
        }
        .platform-logo.gem      { background: rgba(245,158,11,0.15); color: var(--gem); border: 1px solid rgba(245,158,11,0.3); }
        .platform-logo.amazon   { background: rgba(255,153,0,0.12); color: var(--amazon); border: 1px solid rgba(255,153,0,0.25); }
        .platform-logo.flipkart { background: rgba(40,116,240,0.12); color: var(--flipkart); border: 1px solid rgba(40,116,240,0.25); }
        .platform-logo.indiamart{ background: rgba(230,57,70,0.12); color: var(--indiamart); border: 1px solid rgba(230,57,70,0.25); }

        /* ─── CHECKBOX ─── */
        input[type="checkbox"] { accent-color: var(--primary); width: 16px; height: 16px; }

        /* ─── PROGRESS BAR ─── */
        .progress { height: 6px; background: var(--bg3); border-radius: 3px; overflow: hidden; }
        .progress-bar { height: 100%; border-radius: 3px; transition: width 0.6s ease; }

        /* ─── PRINT STYLES ─── */
        @media print {
            .navbar, .no-print { display: none !important; }
            body { background: white; color: black; }
            .card { border: 1px solid #ddd; box-shadow: none; }
        }

        /* ─── TOOLTIP ─── */
        [data-tooltip] { position: relative; cursor: help; }
        [data-tooltip]::after {
            content: attr(data-tooltip); position: absolute; bottom: calc(100% + 8px);
            left: 50%; transform: translateX(-50%); background: #1e1e3a;
            color: white; font-size: 0.75rem; padding: 0.4rem 0.8rem;
            border-radius: 6px; white-space: nowrap; pointer-events: none;
            opacity: 0; transition: opacity 0.2s; border: 1px solid var(--border);
            z-index: 100;
        }
        [data-tooltip]:hover::after { opacity: 1; }

        /* ─── LOADING SPINNER ─── */
        .spinner {
            width: 20px; height: 20px; border: 2px solid var(--border);
            border-top-color: var(--primary); border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ─── FADE IN ANIMATION ─── */
        .fade-in { animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    @yield('head')
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="nav-logo">Nex<span>Buy</span></a>

        <div class="nav-search-wrap">
            <form action="{{ route('search') }}" method="GET" class="nav-search">
                <input type="text" name="q" placeholder="Search products across GeM, Amazon, Flipkart…"
                       value="{{ request('q') }}" autocomplete="off"/>
                <button type="submit">🔍 Search</button>
            </form>
        </div>

        <div class="nav-links">
            <a href="{{ route('search') }}" class="nav-link {{ request()->routeIs('search') ? 'active' : '' }}">🔍 Compare</a>
            <a href="{{ route('tco') }}" class="nav-link {{ request()->routeIs('tco') ? 'active' : '' }}">💰 TCO Calc</a>
            <a href="{{ route('anomalies') }}" class="nav-link {{ request()->routeIs('anomalies') ? 'active' : '' }}">🚨 Alerts</a>
            <a href="{{ route('watchlist') }}" class="nav-link {{ request()->routeIs('watchlist') ? 'active' : '' }}">👁 Watchlist</a>
            <a href="{{ route('ai.matcher') }}" class="nav-link {{ request()->routeIs('ai.matcher') ? 'active' : '' }}" style="color: var(--primary-light);">🤖 AI Matcher</a>
            <a href="{{ route('ai.rfp') }}" class="nav-link {{ request()->routeIs('ai.rfp') ? 'active' : '' }}">📄 Auto-RFP</a>
            <a href="{{ route('ai.ocr') }}" class="nav-link {{ request()->routeIs('ai.ocr') ? 'active' : '' }}">📸 OCR</a>
        </div>
    </div>
</nav>

<!-- FLASH MESSAGE -->
@if(session('success'))
<div style="background:rgba(34,197,94,0.1);border-bottom:1px solid rgba(34,197,94,0.3);color:var(--success);padding:0.75rem 2rem;text-align:center;font-size:0.875rem;">
    ✅ {{ session('success') }}
</div>
@endif

<!-- PAGE CONTENT -->
<main>
    @yield('content')
</main>

<!-- FOOTER -->
<footer style="margin-top:4rem;border-top:1px solid var(--border);padding:2rem;text-align:center;">
    <p style="color:var(--text-muted);font-size:0.85rem;">
        © {{ date('Y') }} <strong style="color:var(--primary);">NexBuy</strong> — GeM Price Comparison Platform &nbsp;|&nbsp;
        Data is representative and for demonstration purposes.
        &nbsp;|&nbsp;
        <a href="{{ route('anomalies') }}" style="color:var(--accent);text-decoration:none;">Report Fraud</a>
    </p>
</footer>

<script>
    // Theme Toggle Logic
    const themeBtn = document.createElement('button');
    themeBtn.innerHTML = '🌓 Toggle Theme';
    themeBtn.style.cssText = 'position:fixed;bottom:20px;left:20px;z-index:9999;padding:10px 15px;background:var(--primary);color:white;border:none;border-radius:50px;cursor:pointer;font-family:Inter;font-weight:bold;box-shadow:0 4px 12px rgba(0,0,0,0.3);';
    document.body.appendChild(themeBtn);

    let isDark = true;
    themeBtn.addEventListener('click', () => {
        isDark = !isDark;
        if(isDark) {
            document.documentElement.style.setProperty('--bg', '#0a0a14');
            document.documentElement.style.setProperty('--bg2', '#12121f');
            document.documentElement.style.setProperty('--bg3', '#1a1a2e');
            document.documentElement.style.setProperty('--text', '#e8e8f0');
            document.documentElement.style.setProperty('--border', 'rgba(255,255,255,0.08)');
        } else {
            document.documentElement.style.setProperty('--bg', '#f8f9fa');
            document.documentElement.style.setProperty('--bg2', '#ffffff');
            document.documentElement.style.setProperty('--bg3', '#f1f3f5');
            document.documentElement.style.setProperty('--text', '#212529');
            document.documentElement.style.setProperty('--border', 'rgba(0,0,0,0.1)');
        }
    });
</script>
@yield('scripts')
</body>
</html>

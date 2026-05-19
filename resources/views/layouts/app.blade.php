<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'NexBuy — NextGen Procurement')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        :root {
            --primary: #7C3AED;
            --primary-glow: rgba(124, 58, 237, 0.5);
            --secondary: #06B6D4;
            --bg-base: #030014;
            --bg-surface: rgba(255, 255, 255, 0.03);
            --bg-surface-hover: rgba(255, 255, 255, 0.06);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --accent: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            
            --gem: #F59E0B;
            --amazon: #FF9900;
            --flipkart: #2874F0;
            --indiamart: #E63946;

            --radius-sm: 12px;
            --radius-md: 20px;
            --radius-lg: 32px;
            
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Animated Mesh Background */
        .bg-mesh {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1;
            background: radial-gradient(circle at 15% 50%, rgba(124,58,237,0.15), transparent 25%),
                        radial-gradient(circle at 85% 30%, rgba(6,182,212,0.15), transparent 25%);
            filter: blur(80px); animation: pulseMesh 15s ease-in-out infinite alternate;
        }
        @keyframes pulseMesh {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(1.1); opacity: 1; }
        }

        /* Typography */
        h1, h2, h3, .font-display { font-family: 'Space Grotesk', sans-serif; }
        
        /* Navbar */
        nav {
            position: sticky; top: 0; z-index: 50;
            background: rgba(3, 0, 20, 0.6); backdrop-filter: blur(24px);
            border-bottom: 1px solid var(--glass-border); padding: 1rem 0;
        }
        .nav-container {
            max-width: 1400px; margin: 0 auto; padding: 0 2rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .logo {
            font-family: 'Space Grotesk', sans-serif; font-size: 1.75rem; font-weight: 700;
            background: linear-gradient(to right, #A78BFA, #67E8F9);
            -webkit-background-clip: text; color: transparent; text-decoration: none;
            display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.5px;
        }
        .nav-links { display: flex; gap: 1rem; align-items: center; }
        .nav-link {
            color: var(--text-muted); text-decoration: none; padding: 0.5rem 1rem;
            border-radius: var(--radius-sm); transition: var(--transition);
            font-weight: 500; font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--text-main); background: var(--bg-surface);
            box-shadow: 0 0 20px rgba(255,255,255,0.02);
        }
        
        /* Modern Search Bar */
        .search-bar {
            flex: 0 1 450px; position: relative;
        }
        .search-bar input {
            width: 100%; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
            padding: 0.8rem 1.5rem 0.8rem 3rem; border-radius: 50px; color: white;
            font-family: inherit; font-size: 0.95rem; transition: var(--transition);
        }
        .search-bar input:focus {
            outline: none; border-color: var(--primary); background: rgba(255,255,255,0.05);
            box-shadow: 0 0 0 4px rgba(124,58,237,0.15);
        }
        .search-bar i { position: absolute; left: 1.2rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); }

        /* Container */
        .container { max-width: 1400px; margin: 0 auto; padding: 3rem 2rem; }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.8rem 1.5rem; border-radius: 50px; font-weight: 600; font-size: 0.95rem;
            text-decoration: none; border: none; cursor: pointer; transition: var(--transition);
            position: relative; overflow: hidden; font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #9333EA); color: white;
            box-shadow: 0 10px 25px -5px var(--primary-glow); border: 1px solid rgba(255,255,255,0.1);
        }
        .btn-primary:hover {
            transform: translateY(-2px) scale(1.02); box-shadow: 0 15px 35px -5px var(--primary-glow);
        }
        .btn-outline {
            background: transparent; color: var(--text-main); border: 1px solid var(--glass-border);
        }
        .btn-outline:hover { background: var(--bg-surface); border-color: rgba(255,255,255,0.3); }
        .btn-ghost { background: var(--bg-surface); color: var(--text-main); border: 1px solid transparent; }
        .btn-ghost:hover { background: var(--bg-surface-hover); }

        /* Cards (Glassmorphism 2.0) */
        .card {
            background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border); border-radius: var(--radius-md);
            transition: var(--transition); overflow: hidden; position: relative;
        }
        .card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            opacity: 0; transition: var(--transition);
        }
        .card:hover {
            transform: translateY(-4px); background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.15); box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .card:hover::before { opacity: 1; }

        /* Badges */
        .badge {
            padding: 0.35rem 0.8rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600;
            display: inline-flex; align-items: center; gap: 0.3rem; letter-spacing: 0.5px; text-transform: uppercase;
        }
        .badge-gem { background: rgba(245,158,11,0.1); color: var(--gem); border: 1px solid rgba(245,158,11,0.2); }
        .badge-amazon { background: rgba(255,153,0,0.1); color: var(--amazon); border: 1px solid rgba(255,153,0,0.2); }
        .badge-flipkart { background: rgba(40,116,240,0.1); color: var(--flipkart); border: 1px solid rgba(40,116,240,0.2); }
        .badge-success { background: rgba(16,185,129,0.1); color: var(--accent); border: 1px solid rgba(16,185,129,0.2); }
        .badge-danger { background: rgba(239,68,68,0.1); color: var(--danger); border: 1px solid rgba(239,68,68,0.2); }
        .badge-purple { background: rgba(124,58,237,0.1); color: #A78BFA; border: 1px solid rgba(124,58,237,0.2); }
        
        /* Grid */
        .grid { display: grid; gap: 1.5rem; }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-auto { grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); }
        
        /* Forms */
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 500; color: var(--text-muted); margin-bottom: 0.5rem; }
        .form-control {
            width: 100%; background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border);
            padding: 0.9rem 1rem; border-radius: var(--radius-sm); color: white;
            font-family: inherit; font-size: 0.95rem; transition: var(--transition);
        }
        .form-control:focus { outline: none; border-color: var(--primary); background: rgba(255,255,255,0.05); }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em; }
        select.form-control option { background: var(--bg-base); color: white; }

        /* Animations */
        .fade-up { animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        
        /* Utilities */
        .text-gradient { background: linear-gradient(to right, #A78BFA, #67E8F9); -webkit-background-clip: text; color: transparent; }
        .flex { display: flex; } .items-center { align-items: center; } .justify-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; } .gap-4 { gap: 1rem; }
        .mb-2 { margin-bottom: 0.5rem; } .mb-4 { margin-bottom: 1rem; } .mb-8 { margin-bottom: 2rem; }
        .mt-4 { margin-top: 1rem; } .mt-8 { margin-top: 2rem; }
        
        /* Alerts */
        .alert { padding: 1rem 1.5rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; backdrop-filter: blur(10px); }
        .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: var(--accent); }
    </style>
    @yield('head')
</head>
<body>
    <div class="bg-mesh"></div>

    <nav>
        <div class="nav-container">
            <a href="{{ route('home') }}" class="logo">
                <i class="ph-fill ph-planet"></i> NexBuy
            </a>
            
            <form action="{{ route('search') }}" method="GET" class="search-bar">
                <i class="ph ph-magnifying-glass"></i>
                <input type="text" name="q" placeholder="Search government & marketplace..." value="{{ request('q') }}" autocomplete="off">
            </form>

            <div class="nav-links">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="ph ph-chart-pie-slice"></i> Dashboard</a>
                <a href="{{ route('search') }}" class="nav-link {{ request()->routeIs('search') ? 'active' : '' }}"><i class="ph ph-scales"></i> Compare</a>
                <a href="{{ route('tco') }}" class="nav-link {{ request()->routeIs('tco') ? 'active' : '' }}"><i class="ph ph-calculator"></i> TCO</a>
                <a href="{{ route('anomalies') }}" class="nav-link {{ request()->routeIs('anomalies') ? 'active' : '' }}"><i class="ph ph-warning-octagon"></i> Alerts</a>
                <a href="{{ route('watchlist') }}" class="nav-link {{ request()->routeIs('watchlist') ? 'active' : '' }}"><i class="ph ph-eye"></i> Watchlist</a>
                <div style="width: 1px; height: 24px; background: var(--glass-border); margin: 0 0.25rem;"></div>
                <a href="{{ route('ai.rfp') }}" class="nav-link" style="color: #A78BFA;"><i class="ph-fill ph-magic-wand"></i> AI</a>
                <div style="width: 1px; height: 24px; background: var(--glass-border); margin: 0 0.25rem;"></div>
                @auth
                <span style="color: var(--text-muted); font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem;"><i class="ph-fill ph-user-circle" style="font-size: 1.3rem; color: #A78BFA;"></i> {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link" style="border: none; cursor: pointer; background: none; font-family: inherit; font-size: 0.95rem;"><i class="ph ph-sign-out"></i> Logout</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="nav-link"><i class="ph ph-sign-in"></i> Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 0.5rem 1.2rem; font-size: 0.85rem;"><i class="ph ph-user-plus"></i> Sign Up</a>
                @endauth
            </div>
        </div>
    </nav>

    @if(session('success'))
    <div class="container" style="padding-bottom: 0;">
        <div class="alert alert-success fade-up">
            <i class="ph-fill ph-check-circle" style="font-size: 1.5rem;"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif

    <main class="container">
        @yield('content')
    </main>

    <footer style="border-top: 1px solid var(--glass-border); padding: 3rem 0; margin-top: 4rem; background: rgba(0,0,0,0.2);">
        <div class="container" style="text-align: center; padding-top: 0; padding-bottom: 0;">
            <div class="logo" style="justify-content: center; margin-bottom: 1rem; filter: grayscale(1) opacity(0.5);">
                <i class="ph-fill ph-planet"></i> NexBuy 2026
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Advanced Procurement Intelligence Platform for GeM & Commercial Markets.<br>
                Empowering government buyers with real-time analytics.
            </p>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>

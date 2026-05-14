@extends('layouts.app')
@section('title', 'NexBuy — Compare GeM, Amazon & Flipkart Prices')

@section('content')
<div class="main">

    <!-- ─── HERO ─── -->
    <section style="padding:3.5rem 0 3rem;text-align:center;" class="fade-in">
        <div style="display:inline-flex;align-items:center;gap:0.5rem;background:rgba(108,99,255,0.12);border:1px solid rgba(108,99,255,0.3);border-radius:50px;padding:0.35rem 1rem;font-size:0.8rem;color:var(--primary-light);margin-bottom:1.5rem;">
            🇮🇳 India's First GeM ↔ Marketplace Price Intelligence Platform
        </div>
        <h1 style="font-family:'Outfit',sans-serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:900;line-height:1.15;margin-bottom:1rem;">
            Stop Overpaying.<br>
            <span style="background:linear-gradient(135deg,var(--primary),var(--accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                Compare GeM vs Market
            </span>
            in Seconds.
        </h1>
        <p style="color:var(--text-muted);font-size:1.1rem;max-width:620px;margin:0 auto 2rem;">
            NexBuy compares Government e-Marketplace (GeM) prices with Amazon, Flipkart & IndiaMART —
            with compliance checks, TCO calculator, fraud detection, and one-click CS reports.
        </p>

        <!-- Hero Search -->
        <form action="{{ route('search') }}" method="GET" style="max-width:600px;margin:0 auto 2.5rem;">
            <div style="display:flex;background:var(--bg2);border:1px solid var(--glass-border);border-radius:50px;overflow:hidden;box-shadow:0 4px 24px rgba(108,99,255,0.15);">
                <input type="text" name="q" placeholder="Search: Laptop, Toner, Chair, UPS…"
                    style="flex:1;border:none;background:none;color:var(--text);padding:1rem 1.5rem;font-size:1rem;outline:none;font-family:'Inter',sans-serif;" autocomplete="off"/>
                <button type="submit" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));border:none;color:white;padding:0.75rem 2rem;font-size:1rem;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;white-space:nowrap;">
                    🔍 Compare Now
                </button>
            </div>
        </form>

        <!-- Platform Badges -->
        <div style="display:flex;justify-content:center;gap:0.75rem;flex-wrap:wrap;">
            <span class="badge badge-gem">🏛 GeM Portal</span>
            <span class="badge badge-amazon">🛒 Amazon</span>
            <span class="badge badge-flipkart">🛍 Flipkart</span>
            <span class="badge badge-indiamart">🏭 IndiaMART</span>
            <span class="badge badge-accent">✅ BIS Compliance</span>
            <span class="badge badge-purple">📊 TCO Calculator</span>
        </div>
    </section>

    <!-- ─── STATS ─── -->
    <section class="grid-4 mb-4">
        <div class="card" style="text-align:center;padding:1.5rem;background:linear-gradient(135deg,rgba(108,99,255,0.12),rgba(108,99,255,0.04));">
            <div style="font-size:2rem;font-family:'Outfit',sans-serif;font-weight:800;color:var(--primary);">{{ number_format($stats['total_products']) }}+</div>
            <div class="text-muted text-sm mt-1">Products Tracked</div>
        </div>
        <div class="card" style="text-align:center;padding:1.5rem;background:linear-gradient(135deg,rgba(0,212,170,0.10),rgba(0,212,170,0.03));">
            <div style="font-size:2rem;font-family:'Outfit',sans-serif;font-weight:800;color:var(--accent);">₹{{ number_format($stats['avg_savings'], 0) }}</div>
            <div class="text-muted text-sm mt-1">Avg Savings Per Product</div>
        </div>
        <div class="card" style="text-align:center;padding:1.5rem;background:linear-gradient(135deg,rgba(245,158,11,0.10),rgba(245,158,11,0.03));">
            <div style="font-size:2rem;font-family:'Outfit',sans-serif;font-weight:800;color:var(--gem);">{{ $stats['categories'] }}</div>
            <div class="text-muted text-sm mt-1">Product Categories</div>
        </div>
        <div class="card" style="text-align:center;padding:1.5rem;background:linear-gradient(135deg,rgba(34,197,94,0.10),rgba(34,197,94,0.03));">
            <div style="font-size:2rem;font-family:'Outfit',sans-serif;font-weight:800;color:var(--success);">{{ $stats['gem_cheaper_count'] }}</div>
            <div class="text-muted text-sm mt-1">Products Cheaper on GeM</div>
        </div>
    </section>

    <!-- ─── QUICK CATEGORY BROWSE ─── -->
    <section class="mb-4">
        <div class="section-title">
            <div class="icon">📂</div> Browse by Category
        </div>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
            @foreach($categories as $cat)
            <a href="{{ route('search', ['category' => $cat]) }}"
               style="background:var(--bg2);border:1px solid var(--border);border-radius:50px;padding:0.5rem 1.25rem;color:var(--text);text-decoration:none;font-size:0.875rem;transition:all 0.2s;display:flex;align-items:center;gap:0.4rem;"
               onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">
                @if($cat == 'IT Equipment') 💻
                @elseif($cat == 'Office Furniture') 🪑
                @elseif($cat == 'Office Supplies') 🖨
                @elseif($cat == 'Stationery') ✏️
                @elseif($cat == 'Cleaning & Hygiene') 🧴
                @elseif($cat == 'Electrical & Power') ⚡
                @else 📦 @endif
                {{ $cat }}
            </a>
            @endforeach
        </div>
    </section>

    <!-- ─── FEATURED PRODUCTS ─── -->
    <section class="mb-4">
        <div class="flex items-center justify-between mb-3">
            <div class="section-title" style="margin-bottom:0;">
                <div class="icon">🔥</div> Featured Comparisons
            </div>
            <a href="{{ route('search') }}" class="btn btn-ghost btn-sm">View All →</a>
        </div>
        <div class="grid-auto">
            @foreach($featuredProducts as $product)
            <div class="card fade-in">
                @if($product->image_url)
                <div style="height:160px;background:var(--bg3);overflow:hidden;position:relative;">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                         style="width:100%;height:100%;object-fit:cover;opacity:0.85;"/>
                    <div style="position:absolute;top:0.75rem;left:0.75rem;display:flex;gap:0.4rem;flex-wrap:wrap;">
                        @if($product->gem_make_in_india)
                        <span class="badge badge-accent" style="font-size:0.65rem;">🇮🇳 Make in India</span>
                        @endif
                        @if($product->gem_bis_certified)
                        <span class="badge badge-success" style="font-size:0.65rem;">✅ BIS</span>
                        @endif
                    </div>
                    @if($product->savings > 0)
                    <div style="position:absolute;top:0.75rem;right:0.75rem;background:var(--success);color:white;font-size:0.7rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:50px;">
                        Save ₹{{ number_format($product->savings, 0) }}
                    </div>
                    @endif
                </div>
                @endif
                <div class="card-body">
                    <div class="text-xs text-muted mb-1">{{ $product->category }}</div>
                    <h3 style="font-size:0.92rem;font-weight:600;margin-bottom:0.75rem;line-height:1.4;">
                        {{ Str::limit($product->name, 60) }}
                    </h3>

                    <!-- Price Comparison Mini -->
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.75rem;">
                        @if($product->gem_price)
                        <div style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25);border-radius:8px;padding:0.35rem 0.65rem;text-align:center;flex:1;">
                            <div style="font-size:0.6rem;color:var(--gem);font-weight:600;">GeM</div>
                            <div style="font-size:0.9rem;font-weight:700;color:var(--gem);">₹{{ number_format($product->gem_price, 0) }}</div>
                        </div>
                        @endif
                        @if($product->amazon_price)
                        <div style="background:rgba(255,153,0,0.08);border:1px solid rgba(255,153,0,0.2);border-radius:8px;padding:0.35rem 0.65rem;text-align:center;flex:1;">
                            <div style="font-size:0.6rem;color:var(--amazon);font-weight:600;">AMZ</div>
                            <div style="font-size:0.9rem;font-weight:700;color:var(--amazon);">₹{{ number_format($product->amazon_price, 0) }}</div>
                        </div>
                        @endif
                        @if($product->flipkart_price)
                        <div style="background:rgba(40,116,240,0.08);border:1px solid rgba(40,116,240,0.2);border-radius:8px;padding:0.35rem 0.65rem;text-align:center;flex:1;">
                            <div style="font-size:0.6rem;color:var(--flipkart);font-weight:600;">FK</div>
                            <div style="font-size:0.9rem;font-weight:700;color:var(--flipkart);">₹{{ number_format($product->flipkart_price, 0) }}</div>
                        </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs text-muted">Best: </span>
                            <span style="font-weight:700;color:var(--accent);">{{ $product->lowest_platform }}</span>
                            <span style="font-size:0.85rem;font-weight:700;color:var(--accent);"> ₹{{ number_format($product->lowest_price, 0) }}</span>
                        </div>
                        <a href="{{ route('product.show', $product) }}" class="btn btn-primary btn-sm">Compare →</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- ─── FEATURES STRIP ─── -->
    <section class="mb-4" style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2.5rem;">
        <div class="section-title" style="justify-content:center;text-align:center;">
            <div class="icon">✨</div> What Makes NexBuy Different
        </div>
        <div class="grid-3" style="gap:2rem;">
            @foreach([
                ['💰', 'TCO Calculator', 'See the *real* cost including GST, shipping, AMC — not just the sticker price.'],
                ['✅', 'Compliance Check', 'Flags BIS certification, Make in India, MSME status for every product.'],
                ['📊', 'Price History', '90-day price charts across GeM, Amazon & Flipkart in one view.'],
                ['🚨', 'Fraud Detection', 'ML-style flags for overpriced GeM listings and counterfeit risk on commercial platforms.'],
                ['📋', 'CS Report Generator', '1-click Comparative Statement PDF — hours of manual work done in seconds.'],
                ['🏷️', 'GeM Premium Score', 'Our proprietary 0-100 score answering: Is GeM actually worth it?'],
            ] as [$icon, $title, $desc])
            <div style="text-align:center;">
                <div style="font-size:2rem;margin-bottom:0.75rem;">{{ $icon }}</div>
                <h3 style="font-weight:700;margin-bottom:0.4rem;font-size:1rem;">{{ $title }}</h3>
                <p style="color:var(--text-muted);font-size:0.85rem;line-height:1.5;">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <!-- ─── CTA ─── -->
    <section style="text-align:center;padding:2rem 0 1rem;">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;margin-bottom:0.75rem;">
            Ready to make smarter procurement decisions?
        </h2>
        <p class="text-muted mb-3">Start comparing prices and save your department lakhs annually.</p>
        <div style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;">
            <a href="{{ route('search') }}" class="btn btn-primary" style="font-size:1rem;padding:0.75rem 2rem;">🔍 Start Comparing</a>
            <a href="{{ route('tco') }}" class="btn btn-outline" style="font-size:1rem;padding:0.75rem 2rem;">💰 Try TCO Calculator</a>
        </div>
    </section>

</div>
@endsection

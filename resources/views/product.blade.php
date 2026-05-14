@extends('layouts.app')
@section('title', $product->name . ' — NexBuy Price Comparison')

@section('content')
<div class="main">

    <!-- Breadcrumb -->
    <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;color:var(--text-muted);margin-bottom:1.5rem;">
        <a href="{{ route('home') }}" style="color:var(--text-muted);text-decoration:none;">Home</a>
        <span>›</span>
        <a href="{{ route('search', ['category' => $product->category]) }}" style="color:var(--text-muted);text-decoration:none;">{{ $product->category }}</a>
        <span>›</span>
        <span style="color:var(--text);">{{ Str::limit($product->name, 50) }}</span>
    </div>

    <!-- ─── PRODUCT HEADER ─── -->
    <div style="display:grid;grid-template-columns:1fr 2fr;gap:2rem;margin-bottom:2rem;" class="fade-in">
        <!-- Image + Score -->
        <div>
            @if($product->image_url)
            <div style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;height:280px;">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                     style="width:100%;height:100%;object-fit:cover;opacity:0.9;"/>
            </div>
            @endif

            <!-- GeM Premium Score -->
            @if($product->gem_premium_score)
            <div style="margin-top:1rem;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;text-align:center;">
                <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.5rem;">GeM Premium Score</div>
                <div style="font-size:3rem;font-weight:900;font-family:'Outfit',sans-serif;
                    color:@if($product->gem_premium_score >= 80) var(--success) @elseif($product->gem_premium_score >= 60) var(--accent) @elseif($product->gem_premium_score >= 40) var(--warning) @else var(--danger) @endif;">
                    {{ $product->gem_premium_score }}
                </div>
                <div style="font-size:0.85rem;font-weight:600;color:var(--text-muted);">{{ $product->gem_score_label }}</div>
                <div style="margin-top:0.75rem;" class="progress">
                    <div class="progress-bar" style="width:{{ $product->gem_premium_score }}%;background:
                        @if($product->gem_premium_score >= 80) var(--success) @elseif($product->gem_premium_score >= 60) var(--accent) @elseif($product->gem_premium_score >= 40) var(--warning) @else var(--danger) @endif;">
                    </div>
                </div>
                <p style="margin-top:0.5rem;font-size:0.75rem;color:var(--text-muted);">Is GeM a better value vs market?</p>
            </div>
            @endif
        </div>

        <!-- Details -->
        <div>
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.75rem;">
                <span class="badge badge-purple">{{ $product->category }}</span>
                @if($product->brand)<span class="badge badge-gem">{{ $product->brand }}</span>@endif
                @if($product->gem_bis_certified)<span class="badge badge-success">✅ BIS Certified</span>@endif
                @if($product->gem_make_in_india)<span class="badge badge-accent">🇮🇳 Make in India</span>@endif
                @if($product->gem_msme_seller)<span class="badge badge-purple">🏢 MSME Seller</span>@endif
            </div>

            <h1 style="font-family:'Outfit',sans-serif;font-size:1.75rem;font-weight:800;line-height:1.3;margin-bottom:0.75rem;">
                {{ $product->name }}
            </h1>

            @if($product->description)
            <p style="color:var(--text-muted);font-size:0.9rem;line-height:1.6;margin-bottom:1.25rem;">{{ $product->description }}</p>
            @endif

            <!-- Best Deal Banner -->
            <div style="background:linear-gradient(135deg,rgba(0,212,170,0.12),rgba(0,212,170,0.04));border:1px solid rgba(0,212,170,0.3);border-radius:var(--radius);padding:1rem 1.25rem;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.2rem;">BEST PRICE AVAILABLE</div>
                    <div style="font-size:2rem;font-weight:900;font-family:'Outfit',sans-serif;color:var(--accent);">
                        ₹{{ number_format($product->lowest_price, 0) }}
                    </div>
                    <div style="font-size:0.85rem;color:var(--text-muted);">on <strong style="color:var(--text)">{{ $product->lowest_platform }}</strong>
                        @if($product->savings > 0)
                        · Save ₹{{ number_format($product->savings, 0) }} vs highest
                        @endif
                    </div>
                </div>
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                    <a href="{{ route('tco', ['product_id' => $product->id]) }}" class="btn btn-outline btn-sm">💰 Calculate TCO</a>
                    <form method="POST" action="{{ route('watchlist.add') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}"/>
                        <button type="submit" class="btn btn-ghost btn-sm">👁 Watch Price</button>
                    </form>
                    <a href="{{ route('cs.report', ['ids' => $product->id]) }}" class="btn btn-ghost btn-sm">📋 CS Report</a>
                </div>
            </div>

            <!-- Platform Comparison Table -->
            <div class="card">
                <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--border);font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">
                    Platform Comparison
                </div>
                @php
                    $platforms = [
                        ['gem',       'GeM Portal', $product->gem_price,      $product->gem_seller,      $product->gem_delivery_days,      $product->gem_warranty_months, $product->gem_bis_certified,      0],
                        ['amazon',    'Amazon',     $product->amazon_price,   $product->amazon_seller,   $product->amazon_delivery_days,   $product->amazon_warranty_months, $product->amazon_bis_certified, $product->amazon_shipping],
                        ['flipkart',  'Flipkart',   $product->flipkart_price, $product->flipkart_seller, $product->flipkart_delivery_days, $product->flipkart_warranty_months, $product->flipkart_bis_certified, $product->flipkart_shipping],
                        ['indiamart', 'IndiaMART',  $product->indiamart_price,$product->indiamart_seller,$product->indiamart_delivery_days, null, false, 0],
                    ];
                @endphp
                @foreach($platforms as [$pKey, $pName, $pPrice, $pSeller, $pDelivery, $pWarranty, $pBis, $pShipping])
                @if($pPrice)
                @php
                    $pColor  = match($pKey) { 'gem' => 'var(--gem)', 'amazon' => 'var(--amazon)', 'flipkart' => 'var(--flipkart)', default => 'var(--indiamart)' };
                    $pIsBest = $product->lowest_platform === $pName;
                    $pStyle  = $pIsBest ? 'background:rgba(0,212,170,0.04);border-left:3px solid var(--accent);' : '';
                @endphp
                <div class="platform-row" style="{{ $pStyle }}">
                    <div class="platform-logo {{ $pKey }}">{{ strtoupper(substr($pName, 0, 3)) }}</div>
                    <div style="flex:1;">
                        <div style="font-weight:700;font-size:0.9rem;color:{{ $pColor }};">{{ $pName }}</div>
                        @if($pSeller)
                        <div class="text-xs text-muted">{{ $pSeller }}</div>
                        @endif
                    </div>
                    <div style="text-align:center;min-width:80px;">
                        <div style="font-size:1.25rem;font-weight:800;font-family:'Outfit',sans-serif;">₹{{ number_format($pPrice, 0) }}</div>
                        @if($pShipping > 0)
                        <div class="text-xs text-muted">+₹{{ $pShipping }} shipping</div>
                        @else
                        <div class="text-xs" style="color:var(--success);">Free shipping</div>
                        @endif
                    </div>
                    <div style="text-align:center;min-width:60px;" class="text-sm text-muted">
                        @if($pDelivery)
                        🚚 {{ $pDelivery }}d
                        @endif
                    </div>
                    <div style="text-align:center;min-width:60px;" class="text-sm text-muted">
                        @if($pWarranty)
                        🛡 {{ $pWarranty }}mo
                        @endif
                    </div>
                    <div style="text-align:center;min-width:40px;">
                        @if($pBis)
                        <span class="badge badge-success" style="font-size:0.6rem;">BIS</span>
                        @endif
                    </div>
                    @if($pIsBest)
                    <span class="badge badge-accent">Best</span>
                    @endif
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- ─── PRICE HISTORY CHART ─── -->
    <div class="card mb-4">
        <div style="padding:1.25rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <h2 style="font-size:1.1rem;font-weight:700;">📊 Price History (Last 90 Days)</h2>
            <div style="display:flex;gap:1rem;font-size:0.8rem;">
                <span style="display:flex;align-items:center;gap:0.4rem;"><span style="width:12px;height:3px;background:var(--gem);display:inline-block;border-radius:2px;"></span>GeM</span>
                <span style="display:flex;align-items:center;gap:0.4rem;"><span style="width:12px;height:3px;background:var(--amazon);display:inline-block;border-radius:2px;"></span>Amazon</span>
                <span style="display:flex;align-items:center;gap:0.4rem;"><span style="width:12px;height:3px;background:var(--flipkart);display:inline-block;border-radius:2px;"></span>Flipkart</span>
            </div>
        </div>
        <div style="padding:1.25rem;height:320px;">
            <canvas id="priceChart"></canvas>
        </div>
    </div>

    <!-- ─── SPECS + SIMILAR PRODUCTS ─── -->
    <div class="grid-2 mb-4">
        <!-- Specifications -->
        @if($product->specifications)
        <div class="card">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--border);font-size:0.85rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">
                📋 Specifications
            </div>
            <div style="padding:1rem;">
                @foreach($product->specifications as $key => $val)
                <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid var(--border);font-size:0.875rem;">
                    <span class="text-muted">{{ $key }}</span>
                    <span style="font-weight:600;">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Similar Products -->
        <div>
            <div class="section-title" style="margin-bottom:1rem;font-size:1.1rem;">🔗 Similar Products</div>
            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                @forelse($similar as $s)
                <a href="{{ route('product.show', $s) }}"
                   style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:0.85rem 1rem;text-decoration:none;display:flex;align-items:center;justify-content:space-between;gap:1rem;transition:all 0.2s;"
                   onmouseover="this.style.borderColor='var(--primary)'"
                   onmouseout="this.style.borderColor='var(--border)'">
                    <div>
                        <div style="font-size:0.85rem;font-weight:600;color:var(--text);">{{ Str::limit($s->name, 50) }}</div>
                        <div class="text-xs text-muted">{{ $s->brand }}</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        @if($s->gem_price)
                        <div style="color:var(--gem);font-weight:700;font-size:0.9rem;">₹{{ number_format($s->gem_price, 0) }}</div>
                        <div class="text-xs text-muted">GeM</div>
                        @endif
                    </div>
                </a>
                @empty
                <p class="text-muted text-sm">No similar products found.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
const ctx = document.getElementById('priceChart').getContext('2d');
const labels = @json($labels);
const gemData = @json($gemData);
const amazonData = @json($amazonData);
const flipkartData = @json($flipkartData);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'GeM',
                data: gemData,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245,158,11,0.08)',
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 5,
                fill: true,
                tension: 0.4,
                spanGaps: true,
            },
            {
                label: 'Amazon',
                data: amazonData,
                borderColor: '#FF9900',
                backgroundColor: 'rgba(255,153,0,0.05)',
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 5,
                fill: false,
                tension: 0.4,
                spanGaps: true,
            },
            {
                label: 'Flipkart',
                data: flipkartData,
                borderColor: '#2874F0',
                backgroundColor: 'rgba(40,116,240,0.05)',
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 5,
                fill: false,
                tension: 0.4,
                spanGaps: true,
            },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1a1a2e',
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1,
                callbacks: {
                    label: ctx => ` ${ctx.dataset.label}: ₹${ctx.parsed.y?.toLocaleString('en-IN') ?? 'N/A'}`
                }
            }
        },
        scales: {
            x: {
                grid: { color: 'rgba(255,255,255,0.04)' },
                ticks: {
                    color: '#8888a8',
                    maxTicksLimit: 12,
                    font: { size: 11 }
                }
            },
            y: {
                grid: { color: 'rgba(255,255,255,0.04)' },
                ticks: {
                    color: '#8888a8',
                    callback: val => '₹' + val.toLocaleString('en-IN'),
                    font: { size: 11 }
                }
            }
        }
    }
});
</script>
@endsection

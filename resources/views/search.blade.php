@extends('layouts.app')
@section('title', 'Search Products — NexBuy')

@section('content')
<div class="main">
    <div class="flex items-center justify-between mb-3">
        <div>
            <div class="page-title">🔍 Search & Compare</div>
            @if($query)
            <p class="text-muted text-sm">{{ $products->total() }} results for "<strong style="color:var(--text)">{{ $query }}</strong>"</p>
            @else
            <p class="text-muted text-sm">Browse {{ $products->total() }} products across all platforms</p>
            @endif
        </div>
        @if($products->count() > 0)
        <a href="{{ route('cs.report', ['ids' => $products->pluck('id')->implode(','), 'quantity' => 1]) }}"
           class="btn btn-ghost btn-sm no-print">📋 Generate CS Report</a>
        @endif
    </div>

    <!-- ─── FILTER BAR ─── -->
    <form method="GET" action="{{ route('search') }}"
          style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;margin-bottom:1.5rem;display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
        <div class="form-group" style="flex:2;min-width:200px;">
            <label class="form-label">Search</label>
            <input type="text" name="q" value="{{ $query }}" class="form-input" placeholder="Product name, brand…"/>
        </div>
        <div class="form-group" style="flex:1;min-width:160px;">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" @selected($category === $cat)>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="flex:1;min-width:140px;">
            <label class="form-label">Platform</label>
            <select name="platform" class="form-select">
                <option value="">All Platforms</option>
                <option value="gem" @selected($platform === 'gem')>GeM Only</option>
                <option value="amazon" @selected($platform === 'amazon')>Amazon Only</option>
                <option value="flipkart" @selected($platform === 'flipkart')>Flipkart Only</option>
            </select>
        </div>
        <div class="form-group" style="flex:1;min-width:140px;">
            <label class="form-label">Sort By</label>
            <select name="sort" class="form-select">
                <option value="name" @selected($sort === 'name')>Name A→Z</option>
                <option value="gem_price" @selected($sort === 'gem_price')>GeM Price ↑</option>
                <option value="amazon_price" @selected($sort === 'amazon_price')>Amazon Price ↑</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="align-self:flex-end;">Apply Filters</button>
        <a href="{{ route('search') }}" class="btn btn-ghost" style="align-self:flex-end;">Reset</a>
    </form>

    <!-- ─── RESULTS GRID ─── -->
    @if($products->count() > 0)
    <div class="grid-auto mb-4">
        @foreach($products as $product)
        <div class="card fade-in">
            @if($product->image_url)
            <div style="height:150px;background:var(--bg3);overflow:hidden;position:relative;">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                     style="width:100%;height:100%;object-fit:cover;opacity:0.8;"/>
                <div style="position:absolute;top:0.5rem;left:0.5rem;display:flex;gap:0.3rem;flex-wrap:wrap;">
                    @if($product->gem_make_in_india)
                    <span class="badge badge-accent" style="font-size:0.6rem;">🇮🇳 MII</span>
                    @endif
                    @if($product->gem_bis_certified || $product->amazon_bis_certified)
                    <span class="badge badge-success" style="font-size:0.6rem;">✅ BIS</span>
                    @endif
                    @if($product->gem_msme_seller)
                    <span class="badge badge-purple" style="font-size:0.6rem;">🏢 MSME</span>
                    @endif
                </div>
                @if($product->savings > 100)
                <div style="position:absolute;top:0.5rem;right:0.5rem;background:rgba(34,197,94,0.9);color:white;font-size:0.65rem;font-weight:700;padding:0.2rem 0.5rem;border-radius:50px;">
                    Save ₹{{ number_format($product->savings, 0) }}
                </div>
                @endif
            </div>
            @endif
            <div class="card-body">
                <div style="display:flex;align-items:start;justify-content:space-between;gap:0.5rem;margin-bottom:0.75rem;">
                    <div>
                        <div class="text-xs text-muted">{{ $product->brand }} · {{ $product->category }}</div>
                        <h3 style="font-size:0.88rem;font-weight:600;line-height:1.4;margin-top:0.2rem;">
                            {{ Str::limit($product->name, 55) }}
                        </h3>
                    </div>
                    @if($product->gem_premium_score)
                    <div style="flex-shrink:0;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:0.3rem 0.6rem;text-align:center;">
                        <div style="font-size:0.65rem;color:var(--text-muted);">Score</div>
                        <div style="font-size:1rem;font-weight:800;color:
                            @if($product->gem_premium_score >= 80) var(--success)
                            @elseif($product->gem_premium_score >= 60) var(--accent)
                            @elseif($product->gem_premium_score >= 40) var(--warning)
                            @else var(--danger) @endif
                        ;">{{ $product->gem_premium_score }}</div>
                    </div>
                    @endif
                </div>

                <!-- Platform Prices -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.4rem;margin-bottom:0.85rem;">
                    @if($product->gem_price)
                    <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:8px;padding:0.4rem 0.5rem;">
                        <div style="font-size:0.58rem;color:var(--gem);font-weight:700;text-transform:uppercase;">GeM</div>
                        <div style="font-size:0.95rem;font-weight:700;color:var(--gem);">₹{{ number_format($product->gem_price, 0) }}</div>
                    </div>
                    @endif
                    @if($product->amazon_price)
                    <div style="background:rgba(255,153,0,0.06);border:1px solid rgba(255,153,0,0.15);border-radius:8px;padding:0.4rem 0.5rem;">
                        <div style="font-size:0.58rem;color:var(--amazon);font-weight:700;text-transform:uppercase;">Amazon</div>
                        <div style="font-size:0.95rem;font-weight:700;color:var(--amazon);">₹{{ number_format($product->amazon_price, 0) }}</div>
                    </div>
                    @endif
                    @if($product->flipkart_price)
                    <div style="background:rgba(40,116,240,0.06);border:1px solid rgba(40,116,240,0.15);border-radius:8px;padding:0.4rem 0.5rem;">
                        <div style="font-size:0.58rem;color:var(--flipkart);font-weight:700;text-transform:uppercase;">Flipkart</div>
                        <div style="font-size:0.95rem;font-weight:700;color:var(--flipkart);">₹{{ number_format($product->flipkart_price, 0) }}</div>
                    </div>
                    @endif
                    @if($product->indiamart_price)
                    <div style="background:rgba(230,57,70,0.06);border:1px solid rgba(230,57,70,0.15);border-radius:8px;padding:0.4rem 0.5rem;">
                        <div style="font-size:0.58rem;color:var(--indiamart);font-weight:700;text-transform:uppercase;">IndiaMART</div>
                        <div style="font-size:0.95rem;font-weight:700;color:var(--indiamart);">₹{{ number_format($product->indiamart_price, 0) }}</div>
                    </div>
                    @endif
                </div>

                <!-- Best deal -->
                <div style="background:rgba(0,212,170,0.06);border:1px solid rgba(0,212,170,0.2);border-radius:8px;padding:0.5rem 0.75rem;margin-bottom:0.75rem;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:0.78rem;color:var(--text-muted);">Best Deal</span>
                    <span style="font-weight:700;color:var(--accent);font-size:0.9rem;">
                        {{ $product->lowest_platform }} — ₹{{ number_format($product->lowest_price, 0) }}
                    </span>
                </div>

                <div class="flex gap-1">
                    <a href="{{ route('product.show', $product) }}" class="btn btn-primary btn-sm" style="flex:1;justify-content:center;">
                        Compare →
                    </a>
                    <form method="POST" action="{{ route('watchlist.add') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}"/>
                        <button type="submit" class="btn btn-ghost btn-sm" title="Add to Watchlist" style="padding:0.4rem 0.7rem;">👁</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- PAGINATION -->
    <div style="display:flex;justify-content:center;">
        {{ $products->links() }}
    </div>

    @else
    <div style="text-align:center;padding:4rem 2rem;">
        <div style="font-size:4rem;margin-bottom:1rem;">🔍</div>
        <h3 style="font-size:1.4rem;font-weight:700;margin-bottom:0.5rem;">No products found</h3>
        <p class="text-muted mb-3">Try a different keyword or category</p>
        <a href="{{ route('search') }}" class="btn btn-primary">Browse All Products</a>
    </div>
    @endif
</div>
@endsection

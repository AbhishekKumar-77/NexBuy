@extends('layouts.app')
@section('title', 'Watchlist — NexBuy')

@section('content')
<div class="main">
    <div class="page-title">👁 My Watchlist</div>
    <p class="text-muted mb-4">Track products and get notified when prices drop to your target.</p>

    @if($items->count() > 0)
    <div style="display:flex;flex-direction:column;gap:1rem;">
        @foreach($items as $item)
        @php $p = $item->product; @endphp
        <div class="card fade-in">
            <div style="padding:1rem 1.25rem;display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;">
                <!-- Image -->
                @if($p->image_url)
                <div style="width:80px;height:70px;border-radius:8px;overflow:hidden;flex-shrink:0;">
                    <img src="{{ $p->image_url }}" style="width:100%;height:100%;object-fit:cover;opacity:0.85;"/>
                </div>
                @endif

                <!-- Info -->
                <div style="flex:1;min-width:200px;">
                    <div class="text-xs text-muted">{{ $p->category }} · {{ $p->brand }}</div>
                    <a href="{{ route('product.show', $p) }}" style="font-size:0.95rem;font-weight:700;color:var(--text);text-decoration:none;line-height:1.4;display:block;margin-top:0.2rem;">
                        {{ Str::limit($p->name, 65) }}
                    </a>
                    <div style="display:flex;gap:0.4rem;flex-wrap:wrap;margin-top:0.4rem;">
                        @if($p->gem_bis_certified)<span class="badge badge-success" style="font-size:0.6rem;">✅ BIS</span>@endif
                        @if($p->gem_make_in_india)<span class="badge badge-accent" style="font-size:0.6rem;">🇮🇳 MII</span>@endif
                        @if($p->gem_msme_seller)<span class="badge badge-purple" style="font-size:0.6rem;">MSME</span>@endif
                    </div>
                </div>

                <!-- Prices -->
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                    @if($p->gem_price)
                    <div style="text-align:center;">
                        <div style="font-size:0.65rem;color:var(--gem);font-weight:700;text-transform:uppercase;">GeM</div>
                        <div style="font-size:1rem;font-weight:800;color:var(--gem);">₹{{ number_format($p->gem_price, 0) }}</div>
                    </div>
                    @endif
                    @if($p->amazon_price)
                    <div style="text-align:center;">
                        <div style="font-size:0.65rem;color:var(--amazon);font-weight:700;text-transform:uppercase;">Amazon</div>
                        <div style="font-size:1rem;font-weight:800;color:var(--amazon);">₹{{ number_format($p->amazon_price, 0) }}</div>
                    </div>
                    @endif
                    @if($p->flipkart_price)
                    <div style="text-align:center;">
                        <div style="font-size:0.65rem;color:var(--flipkart);font-weight:700;text-transform:uppercase;">Flipkart</div>
                        <div style="font-size:1rem;font-weight:800;color:var(--flipkart);">₹{{ number_format($p->flipkart_price, 0) }}</div>
                    </div>
                    @endif
                </div>

                <!-- Best price -->
                <div style="text-align:center;background:rgba(0,212,170,0.08);border:1px solid rgba(0,212,170,0.25);border-radius:10px;padding:0.5rem 0.85rem;min-width:110px;">
                    <div class="text-xs text-muted">Best Deal</div>
                    <div style="font-size:1.1rem;font-weight:900;font-family:'Outfit',sans-serif;color:var(--accent);">₹{{ number_format($p->lowest_price, 0) }}</div>
                    <div class="text-xs" style="color:var(--accent);">{{ $p->lowest_platform }}</div>
                </div>

                <!-- Actions -->
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                    <a href="{{ route('product.show', $p) }}" class="btn btn-primary btn-sm">View →</a>
                    <form method="POST" action="{{ route('watchlist.remove') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $p->id }}"/>
                        <button type="submit" class="btn btn-danger btn-sm w-full">Remove</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Summary -->
    <div style="margin-top:2rem;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;display:flex;gap:2rem;flex-wrap:wrap;">
        <div>
            <div class="text-xs text-muted">Products Tracked</div>
            <div style="font-size:1.5rem;font-weight:800;font-family:'Outfit',sans-serif;color:var(--primary);">{{ $items->count() }}</div>
        </div>
        <div>
            <div class="text-xs text-muted">Total Potential Savings</div>
            <div style="font-size:1.5rem;font-weight:800;font-family:'Outfit',sans-serif;color:var(--accent);">
                ₹{{ number_format($items->sum(fn($i) => $i->product->savings), 0) }}
            </div>
        </div>
        <div style="margin-left:auto;align-self:center;">
            <a href="{{ route('cs.report', ['ids' => $items->pluck('product_id')->implode(',')]) }}"
               class="btn btn-ghost btn-sm">📋 Generate CS Report for All</a>
        </div>
    </div>

    @else
    <div style="text-align:center;padding:5rem 2rem;">
        <div style="font-size:5rem;margin-bottom:1rem;">👁</div>
        <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:0.5rem;">Your watchlist is empty</h2>
        <p class="text-muted mb-3">Start tracking products by clicking the 👁 icon on any product card.</p>
        <a href="{{ route('search') }}" class="btn btn-primary">Browse Products</a>
    </div>
    @endif
</div>
@endsection

@extends('layouts.app')
@section('title', 'Intelligence Search — NexBuy')

@section('content')
<div class="fade-up">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Market Intelligence</h1>
            @if($query)
            <p style="color: var(--text-muted); font-size: 1.1rem;">Found {{ $products->total() }} matches for "<span style="color: white; font-weight: 600;">{{ $query }}</span>"</p>
            @else
            <p style="color: var(--text-muted); font-size: 1.1rem;">Monitoring {{ $products->total() }} distinct SKUs across multiple vendors</p>
            @endif
        </div>
        @if($products->count() > 0)
        <a href="{{ route('cs.report', ['ids' => $products->pluck('id')->implode(','), 'quantity' => 1]) }}" class="btn btn-primary">
            <i class="ph-fill ph-file-pdf"></i> Export CS Matrix
        </a>
        @endif
    </div>

    <!-- Filter Bar (Glassmorphism) -->
    <div class="card mb-8" style="padding: 1.5rem; overflow: visible;">
        <form method="GET" action="{{ route('search') }}" style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 250px;">
                <label class="form-label">Search Query</label>
                <div style="position: relative;">
                    <i class="ph ph-magnifying-glass" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="text" name="q" value="{{ $query }}" class="form-control" placeholder="SKU, Brand, or Specification..." style="padding-left: 2.5rem;">
                </div>
            </div>
            <div style="flex: 1; min-width: 180px;">
                <label class="form-label">Category</label>
                <select name="category" class="form-control">
                    <option value="">Global Market</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" @selected($category === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label">Source Priority</label>
                <select name="platform" class="form-control">
                    <option value="">All Sources</option>
                    <option value="gem" @selected($platform === 'gem')>GeM Network</option>
                    <option value="amazon" @selected($platform === 'amazon')>Amazon B2B</option>
                    <option value="flipkart" @selected($platform === 'flipkart')>Flipkart</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label">Sort Algorithm</label>
                <select name="sort" class="form-control">
                    <option value="name" @selected($sort === 'name')>Alphabetical</option>
                    <option value="gem_price" @selected($sort === 'gem_price')>GeM Price (Asc)</option>
                    <option value="amazon_price" @selected($sort === 'amazon_price')>Amazon Price (Asc)</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem; min-width: max-content;">
                <button type="submit" class="btn btn-primary"><i class="ph ph-funnel"></i> Filter</button>
                <a href="{{ route('search') }}" class="btn btn-ghost"><i class="ph ph-x"></i></a>
            </div>
        </form>
    </div>

    <!-- Results Grid -->
    @if($products->count() > 0)
    <div class="grid grid-auto mb-8">
        @foreach($products as $product)
        <div class="card" style="display: flex; flex-direction: column;">
            @if($product->image_url)
            <div style="height: 180px; position: relative; background: #000; overflow: hidden;">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;">
                <div style="position: absolute; top: 0.75rem; left: 0.75rem; display: flex; gap: 0.4rem; flex-wrap: wrap;">
                    @if($product->gem_make_in_india) <span class="badge badge-purple" style="font-size: 0.65rem;">🇮🇳 MII</span> @endif
                    @if($product->gem_bis_certified) <span class="badge badge-success" style="font-size: 0.65rem;">✅ BIS</span> @endif
                    @if($product->gem_msme_seller) <span class="badge" style="background: rgba(6,182,212,0.1); color: var(--secondary); border: 1px solid rgba(6,182,212,0.2); font-size: 0.65rem;">🏢 MSME</span> @endif
                </div>
            </div>
            @endif
            
            <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs" style="color: var(--secondary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">{{ $product->brand }} · {{ $product->category }}</div>
                    @if($product->gem_premium_score)
                    <div style="display: flex; align-items: center; gap: 0.3rem; background: rgba(255,255,255,0.05); padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; border: 1px solid var(--glass-border);">
                        <i class="ph-fill ph-star" style="color: var(--gem);"></i> {{ $product->gem_premium_score }}
                    </div>
                    @endif
                </div>
                
                <h3 style="font-size: 1.05rem; font-weight: 600; line-height: 1.4; margin-bottom: 1.5rem; flex: 1;">
                    {{ Str::limit($product->name, 55) }}
                </h3>

                <!-- Advanced Price Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1.5rem;">
                    @if($product->gem_price)
                    <div style="background: rgba(245,158,11,0.05); border: 1px solid rgba(245,158,11,0.15); border-radius: 8px; padding: 0.6rem; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <span style="font-size: 0.65rem; color: var(--gem); font-weight: 700; text-transform: uppercase;">GeM Portal</span>
                        <span style="font-size: 1.1rem; font-weight: 700; color: white;">₹{{ number_format($product->gem_price, 0) }}</span>
                    </div>
                    @endif
                    @if($product->amazon_price)
                    <div style="background: rgba(255,153,0,0.05); border: 1px solid rgba(255,153,0,0.15); border-radius: 8px; padding: 0.6rem; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <span style="font-size: 0.65rem; color: var(--amazon); font-weight: 700; text-transform: uppercase;">Amazon B2B</span>
                        <span style="font-size: 1.1rem; font-weight: 700; color: white;">₹{{ number_format($product->amazon_price, 0) }}</span>
                    </div>
                    @endif
                    @if($product->flipkart_price && !$product->amazon_price)
                    <div style="background: rgba(40,116,240,0.05); border: 1px solid rgba(40,116,240,0.15); border-radius: 8px; padding: 0.6rem; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <span style="font-size: 0.65rem; color: var(--flipkart); font-weight: 700; text-transform: uppercase;">Flipkart</span>
                        <span style="font-size: 1.1rem; font-weight: 700; color: white;">₹{{ number_format($product->flipkart_price, 0) }}</span>
                    </div>
                    @endif
                </div>

                <!-- Best Deal Banner -->
                <div style="background: linear-gradient(90deg, rgba(16,185,129,0.1), rgba(16,185,129,0.05)); border: 1px solid rgba(16,185,129,0.2); border-radius: 8px; padding: 0.6rem 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.75rem; color: var(--accent); font-weight: 600; text-transform: uppercase;"><i class="ph-fill ph-lightning"></i> Best Route</span>
                    <span style="font-weight: 700; color: white; font-size: 0.95rem;">
                        {{ $product->lowest_platform }}
                    </span>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('product.show', $product) }}" class="btn btn-outline" style="flex: 1; padding: 0.6rem;">
                        Analyze <i class="ph ph-arrow-right"></i>
                    </a>
                    <form method="POST" action="{{ route('watchlist.add') }}" style="display: inline;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}"/>
                        <button type="submit" class="btn btn-ghost" style="padding: 0.6rem 1rem;" title="Watch"><i class="ph ph-eye"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination styling using Laravel defaults customized via css if needed, but we wrap it -->
    <div style="display: flex; justify-content: center; background: rgba(255,255,255,0.02); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--glass-border);">
        {{ $products->links() }}
    </div>

    @else
    <div style="text-align: center; padding: 6rem 2rem; border: 1px dashed var(--glass-border); border-radius: var(--radius-lg); background: rgba(255,255,255,0.01);">
        <i class="ph ph-magnifying-glass" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1.5rem;"></i>
        <h3 class="font-display" style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">No Intelligence Found</h3>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Adjust your filter parameters or try a broader search query.</p>
        <a href="{{ route('search') }}" class="btn btn-primary">Reset Filters</a>
    </div>
    @endif
</div>
@endsection

@extends('layouts.app')
@section('title', 'Active Watchlist — NexBuy')

@section('content')
<div class="fade-up">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="ph-fill ph-radar text-gradient"></i> Target Acquisition
            </h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Active monitoring of critical procurement assets.</p>
        </div>
        @if($items->count() > 0)
        <div style="text-align: right;">
            <div style="font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.3rem;">Projected Optimization</div>
            <div class="font-display" style="font-size: 2rem; font-weight: 800; color: var(--accent); line-height: 1;">
                ₹{{ number_format($items->sum(fn($i) => $i->product->savings), 0) }}
            </div>
        </div>
        @endif
    </div>

    @if($items->count() > 0)
    <div class="grid grid-2 mb-8">
        @foreach($items as $item)
        @php $p = $item->product; @endphp
        <div class="card" style="display: flex; padding: 1.5rem; gap: 1.5rem; align-items: stretch;">
            @if($p->image_url)
            <div style="width: 120px; flex-shrink: 0; background: #000; border-radius: var(--radius-sm); overflow: hidden; position: relative;">
                <img src="{{ $p->image_url }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8;">
            </div>
            @endif
            
            <div style="flex: 1; display: flex; flex-direction: column;">
                <div style="font-size: 0.75rem; color: var(--secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 1px; margin-bottom: 0.3rem;">{{ $p->brand }}</div>
                <h3 style="font-size: 1.1rem; font-weight: 600; line-height: 1.4; color: white; margin-bottom: 1rem;">
                    {{ Str::limit($p->name, 50) }}
                </h3>
                
                <div style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem; flex: 1;">
                    @if($p->gem_price)
                    <div>
                        <div style="font-size: 0.7rem; color: var(--gem); text-transform: uppercase; font-weight: 700; margin-bottom: 0.2rem;">GeM</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: white;">₹{{ number_format($p->gem_price, 0) }}</div>
                    </div>
                    @endif
                    @if($p->amazon_price)
                    <div>
                        <div style="font-size: 0.7rem; color: var(--amazon); text-transform: uppercase; font-weight: 700; margin-bottom: 0.2rem;">Amazon</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: white;">₹{{ number_format($p->amazon_price, 0) }}</div>
                    </div>
                    @endif
                </div>
                
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('product.show', $p) }}" class="btn btn-outline" style="flex: 1; padding: 0.6rem; font-size: 0.85rem;">Access Data</a>
                    <form method="POST" action="{{ route('watchlist.remove') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $p->id }}"/>
                        <button type="submit" class="btn btn-ghost" style="padding: 0.6rem 1rem; color: var(--danger); background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);"><i class="ph ph-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Batch Action -->
    <div class="card" style="padding: 2rem; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(90deg, rgba(124,58,237,0.1), transparent);">
        <div>
            <h3 class="font-display" style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.25rem;">Generate Batch Matrix</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Export Comparative Statement for all monitored assets.</p>
        </div>
        <a href="{{ route('cs.report', ['ids' => $items->pluck('product_id')->implode(',')]) }}" class="btn btn-primary">
            <i class="ph-fill ph-file-pdf"></i> Execute Batch CS
        </a>
    </div>

    @else
    <div style="text-align: center; padding: 8rem 2rem; border: 1px dashed var(--glass-border); border-radius: var(--radius-lg); background: rgba(255,255,255,0.01);">
        <i class="ph-fill ph-radar" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1.5rem; opacity: 0.5;"></i>
        <h3 class="font-display" style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">Radar Clear</h3>
        <p style="color: var(--text-muted); margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto;">You are not currently monitoring any procurement assets. Add items from the Intelligence Search to track their vectors.</p>
        <a href="{{ route('search') }}" class="btn btn-primary"><i class="ph ph-magnifying-glass"></i> Access Market Data</a>
    </div>
    @endif
</div>
@endsection

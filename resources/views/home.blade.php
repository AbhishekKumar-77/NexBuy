@extends('layouts.app')
@section('title', 'NexBuy — Next-Gen Procurement Intelligence')

@section('content')
<!-- Hero Section -->
<section style="text-align: center; padding: 6rem 0; position: relative;" class="fade-up">
    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(124, 58, 237, 0.1); border: 1px solid rgba(124, 58, 237, 0.3); padding: 0.5rem 1.25rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600; color: #C4B5FD; margin-bottom: 2rem;">
        <span style="display: inline-block; width: 8px; height: 8px; background: #34D399; border-radius: 50%; box-shadow: 0 0 10px #34D399;"></span>
        Live Intelligence Engine v2.0
    </div>
    
    <h1 class="font-display" style="font-size: clamp(3rem, 6vw, 4.5rem); font-weight: 800; line-height: 1.1; margin-bottom: 1.5rem; letter-spacing: -1px;">
        Procurement without <br>
        <span class="text-gradient">compromise.</span>
    </h1>
    
    <p style="color: var(--text-muted); font-size: 1.15rem; max-width: 600px; margin: 0 auto 3rem; line-height: 1.6;">
        Instantly audit GeM pricing against Amazon, Flipkart, and IndiaMART. Detect anomalies, generate CS reports, and optimize your TCO in milliseconds.
    </p>

    <div style="display: flex; justify-content: center; gap: 1rem;">
        <a href="{{ route('search') }}" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.05rem;">
            <i class="ph-fill ph-rocket"></i> Start Engine
        </a>
        <a href="{{ route('tco') }}" class="btn btn-outline" style="padding: 1rem 2rem; font-size: 1.05rem;">
            <i class="ph ph-chart-line-up"></i> TCO Analysis
        </a>
    </div>
</section>

<!-- Stats Overview -->
<section class="grid grid-4 mb-8" style="animation-delay: 0.2s;">
    @php
        $statsData = [
            ['icon' => 'ph-cube', 'label' => 'Products Tracked', 'value' => number_format($stats['total_products']).'+', 'color' => '#8B5CF6'],
            ['icon' => 'ph-piggy-bank', 'label' => 'Avg Savings', 'value' => '₹'.number_format($stats['avg_savings'], 0), 'color' => '#10B981'],
            ['icon' => 'ph-squares-four', 'label' => 'Categories', 'value' => $stats['categories'], 'color' => '#F59E0B'],
            ['icon' => 'ph-trend-down', 'label' => 'GeM Cheaper', 'value' => $stats['gem_cheaper_count'], 'color' => '#06B6D4']
        ];
    @endphp
    @foreach($statsData as $s)
    <div class="card" style="padding: 2rem; text-align: center; border-top: 3px solid {{ $s['color'] }}40;">
        <i class="ph {{ $s['icon'] }}" style="font-size: 2.5rem; color: {{ $s['color'] }}; margin-bottom: 1rem;"></i>
        <div class="font-display" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; color: {{ $s['color'] }}">{{ $s['value'] }}</div>
        <div style="color: var(--text-muted); font-size: 0.9rem; font-weight: 500; text-transform: uppercase; letter-spacing: 1px;">{{ $s['label'] }}</div>
    </div>
    @endforeach
</section>

<!-- Featured Comparisons -->
<section class="mb-8 fade-up" style="animation-delay: 0.4s;">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-display" style="font-size: 2rem; font-weight: 700;"><i class="ph-fill ph-fire text-gradient"></i> Trending Comparisons</h2>
        <a href="{{ route('search') }}" class="btn btn-ghost">View All Markets <i class="ph ph-arrow-right"></i></a>
    </div>
    
    <div class="grid grid-auto">
        @foreach($featuredProducts as $product)
        <div class="card" style="display: flex; flex-direction: column;">
            @if($product->image_url)
            <div style="height: 200px; position: relative; overflow: hidden; background: #111;">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7; transition: var(--transition);" onmouseover="this.style.transform='scale(1.05)'; this.style.opacity='1';" onmouseout="this.style.transform='scale(1)'; this.style.opacity='0.7';">
                <div style="position: absolute; top: 1rem; left: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    @if($product->gem_make_in_india)
                    <span class="badge badge-purple" style="backdrop-filter: blur(4px);">🇮🇳 MII</span>
                    @endif
                    @if($product->gem_bis_certified)
                    <span class="badge badge-success" style="backdrop-filter: blur(4px);">✅ BIS</span>
                    @endif
                </div>
                @if($product->savings > 0)
                <div style="position: absolute; bottom: 1rem; right: 1rem; background: rgba(16,185,129,0.9); backdrop-filter: blur(8px); color: white; padding: 0.4rem 0.8rem; border-radius: var(--radius-sm); font-weight: 700; font-size: 0.85rem; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                    Save ₹{{ number_format($product->savings, 0) }}
                </div>
                @endif
            </div>
            @endif
            
            <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                <div style="color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">{{ $product->category }}</div>
                <h3 style="font-size: 1.1rem; font-weight: 600; line-height: 1.4; margin-bottom: 1.5rem; flex: 1;">
                    {{ Str::limit($product->name, 60) }}
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem;">
                    @if($product->gem_price)
                    <div style="background: rgba(245,158,11,0.05); border: 1px solid rgba(245,158,11,0.1); border-radius: var(--radius-sm); padding: 0.75rem; text-align: center;">
                        <div style="font-size: 0.7rem; color: var(--gem); font-weight: 700; text-transform: uppercase; margin-bottom: 0.2rem;">GeM</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: white;">₹{{ number_format($product->gem_price, 0) }}</div>
                    </div>
                    @endif
                    @if($product->amazon_price || $product->flipkart_price)
                    @php
                        $marketPrice = $product->amazon_price ?: $product->flipkart_price;
                        $marketName = $product->amazon_price ? 'Amazon' : 'Flipkart';
                        $marketColor = $product->amazon_price ? 'var(--amazon)' : 'var(--flipkart)';
                    @endphp
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); padding: 0.75rem; text-align: center;">
                        <div style="font-size: 0.7rem; color: {{ $marketColor }}; font-weight: 700; text-transform: uppercase; margin-bottom: 0.2rem;">{{ $marketName }}</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: white;">₹{{ number_format($marketPrice, 0) }}</div>
                    </div>
                    @endif
                </div>
                
                <a href="{{ route('product.show', $product) }}" class="btn btn-outline" style="width: 100%;">
                    Deep Dive <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Features Grid -->
<section class="mb-8 fade-up" style="animation-delay: 0.6s;">
    <div style="background: linear-gradient(145deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 4rem; position: relative; overflow: hidden;">
        <div style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(124,58,237,0.05) 0%, transparent 50%); pointer-events: none;"></div>
        
        <h2 class="font-display text-center mb-8" style="font-size: 2.5rem; font-weight: 700;">Platform Capabilities</h2>
        
        <div class="grid grid-3">
            @foreach([
                ['icon' => 'ph-calculator', 'title' => 'TCO Engine', 'desc' => 'Calculate true cost of ownership factoring in GST, logistics, and AMC.'],
                ['icon' => 'ph-shield-check', 'title' => 'Compliance Matrix', 'desc' => 'Instant verification of BIS certification, Make in India, and MSME flags.'],
                ['icon' => 'ph-warning-octagon', 'title' => 'Fraud Detection', 'desc' => 'Algorithmic detection of price gouging and counterfeit risk indicators.'],
            ] as $feature)
            <div style="text-align: left; padding: 2rem; background: rgba(0,0,0,0.2); border-radius: var(--radius-md); border: 1px solid rgba(255,255,255,0.05); transition: var(--transition);" onmouseover="this.style.background='rgba(255,255,255,0.05)'; this.style.borderColor='var(--primary)';" onmouseout="this.style.background='rgba(0,0,0,0.2)'; this.style.borderColor='rgba(255,255,255,0.05)';">
                <div style="width: 50px; height: 50px; background: rgba(124,58,237,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; border: 1px solid rgba(124,58,237,0.3);">
                    <i class="ph {{ $feature['icon'] }}" style="font-size: 1.8rem; color: #A78BFA;"></i>
                </div>
                <h3 class="font-display" style="font-size: 1.2rem; margin-bottom: 0.75rem;">{{ $feature['title'] }}</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

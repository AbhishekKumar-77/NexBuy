@extends('layouts.app')
@section('title', $product->name . ' - Intelligence - NexBuy')

@section('content')
<div class="fade-up">
    <!-- Back & Breadcrumb -->
    <div class="mb-4">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('search') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: var(--transition);" onmouseover="this.style.color='white'" onmouseout="this.style.color='var(--text-muted)'">
            <i class="ph ph-arrow-left"></i> Return to Intelligence Search
        </a>
    </div>

    <!-- Main Header -->
    <div class="grid grid-2 mb-8" style="grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;">
        <div>
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--glass-border);">{{ $product->category }}</span>
                <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--glass-border);">{{ $product->brand }}</span>
                @if($product->gem_make_in_india)
                <span class="badge badge-purple">🇮🇳 MII verified</span>
                @endif
                @if($product->gem_bis_certified)
                <span class="badge badge-success">✅ BIS compliant</span>
                @endif
            </div>
            <h1 class="font-display" style="font-size: 2.5rem; font-weight: 700; line-height: 1.2; margin-bottom: 1.5rem;">
                {{ $product->name }}
            </h1>
            <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.6; margin-bottom: 2rem;">
                Detailed procurement intelligence and real-time market pricing analysis for GeM vs Commercial vendors.
            </p>
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('tco') }}?product_id={{ $product->id }}" class="btn btn-primary" style="padding: 0.8rem 2rem;">
                    <i class="ph-fill ph-calculator"></i> Run TCO Analysis
                </a>
                <form method="POST" action="{{ route('watchlist.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn btn-outline" style="padding: 0.8rem 2rem;">
                        <i class="ph ph-eye"></i> Track Asset
                    </button>
                </form>
            </div>
        </div>
        
        @if($product->image_url)
        <div style="position: relative;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 120%; height: 120%; background: radial-gradient(circle, rgba(124,58,237,0.2) 0%, transparent 70%); z-index: -1;"></div>
            <div class="card" style="padding: 0.5rem; border-radius: var(--radius-lg);">
                <img src="{{ $product->image_url }}" style="width: 100%; height: auto; border-radius: var(--radius-md); object-fit: cover;">
            </div>
            
            @if($product->savings > 0)
            <div style="position: absolute; bottom: -20px; right: -20px; background: rgba(16,185,129,0.9); backdrop-filter: blur(10px); padding: 1rem 1.5rem; border-radius: var(--radius-md); border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 10px 30px rgba(0,0,0,0.5); text-align: center;">
                <div style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: rgba(255,255,255,0.8); margin-bottom: 0.2rem;">Max Optimization</div>
                <div class="font-display" style="font-size: 1.8rem; font-weight: 800; color: white; line-height: 1;">₹{{ number_format($product->savings, 0) }}</div>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Comparative Matrix -->
    <h2 class="font-display mb-4" style="font-size: 1.75rem; font-weight: 700;">Price Intelligence Matrix</h2>
    
    <div class="grid mb-8" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        @if($product->gem_price)
        <div class="card" style="padding: 2rem; position: relative; overflow: hidden; border-top: 3px solid var(--gem);">
            <div style="position: absolute; top: 0; right: 0; padding: 1rem;">
                <i class="ph-fill ph-bank" style="font-size: 2rem; color: rgba(245,158,11,0.2);"></i>
            </div>
            <div style="font-size: 0.85rem; color: var(--gem); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">GeM Network</div>
            <div class="font-display" style="font-size: 2.5rem; font-weight: 800; color: white; margin-bottom: 0.5rem; line-height: 1;">₹{{ number_format($product->gem_price, 0) }}</div>
            <div style="color: var(--text-muted); font-size: 0.9rem;">Primary government channel</div>
        </div>
        @endif
        
        @if($product->amazon_price)
        <div class="card" style="padding: 2rem; position: relative; overflow: hidden; border-top: 3px solid var(--amazon);">
            <div style="position: absolute; top: 0; right: 0; padding: 1rem;">
                <i class="ph-fill ph-shopping-cart" style="font-size: 2rem; color: rgba(255,153,0,0.2);"></i>
            </div>
            <div style="font-size: 0.85rem; color: var(--amazon); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">Amazon B2B</div>
            <div class="font-display" style="font-size: 2.5rem; font-weight: 800; color: white; margin-bottom: 0.5rem; line-height: 1;">₹{{ number_format($product->amazon_price, 0) }}</div>
            <div style="color: var(--text-muted); font-size: 0.9rem;">Commercial benchmark</div>
        </div>
        @endif

        @if($product->flipkart_price)
        <div class="card" style="padding: 2rem; position: relative; overflow: hidden; border-top: 3px solid var(--flipkart);">
            <div style="position: absolute; top: 0; right: 0; padding: 1rem;">
                <i class="ph-fill ph-storefront" style="font-size: 2rem; color: rgba(40,116,240,0.2);"></i>
            </div>
            <div style="font-size: 0.85rem; color: var(--flipkart); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">Flipkart</div>
            <div class="font-display" style="font-size: 2.5rem; font-weight: 800; color: white; margin-bottom: 0.5rem; line-height: 1;">₹{{ number_format($product->flipkart_price, 0) }}</div>
            <div style="color: var(--text-muted); font-size: 0.9rem;">Commercial benchmark</div>
        </div>
        @endif
        
        <div class="card" style="padding: 2rem; position: relative; overflow: hidden; background: linear-gradient(135deg, rgba(16,185,129,0.1), transparent); border: 1px solid rgba(16,185,129,0.3);">
            <div style="position: absolute; top: 0; right: 0; padding: 1rem;">
                <i class="ph-fill ph-lightning" style="font-size: 2rem; color: rgba(16,185,129,0.2);"></i>
            </div>
            <div style="font-size: 0.85rem; color: var(--accent); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">Optimal Route: {{ $product->lowest_platform }}</div>
            <div class="font-display" style="font-size: 2.5rem; font-weight: 800; color: var(--accent); margin-bottom: 0.5rem; line-height: 1;">₹{{ number_format($product->lowest_price, 0) }}</div>
            <div style="color: var(--text-muted); font-size: 0.9rem;">Lowest acquisition cost</div>
        </div>
    </div>

    <!-- Specifications Table -->
    <div class="card mb-8" style="padding: 2rem;">
        <h3 class="font-display mb-6" style="font-size: 1.25rem; font-weight: 600; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">Technical Specifications</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <tbody>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 1rem; width: 30%; color: var(--text-muted); font-weight: 500;">Product Name</th>
                    <td style="padding: 1rem; font-weight: 600; color: white;">{{ $product->name }}</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 1rem; width: 30%; color: var(--text-muted); font-weight: 500;">Brand</th>
                    <td style="padding: 1rem; font-weight: 600; color: white;">{{ $product->brand }}</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 1rem; width: 30%; color: var(--text-muted); font-weight: 500;">Category</th>
                    <td style="padding: 1rem; font-weight: 600; color: white;">{{ $product->category }}</td>
                </tr>
                @if($product->specifications && is_array($product->specifications))
                @foreach($product->specifications as $specKey => $specVal)
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 1rem; width: 30%; color: var(--text-muted); font-weight: 500;">{{ $specKey }}</th>
                    <td style="padding: 1rem; font-weight: 600; color: white;">{{ $specVal }}</td>
                </tr>
                @endforeach
                @endif
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 1rem; width: 30%; color: var(--text-muted); font-weight: 500;">GeM Intelligence Score</th>
                    <td style="padding: 1rem;">
                        @if($product->gem_premium_score >= 80)
                        <span class="badge badge-success" style="font-size: 0.9rem;"><i class="ph-fill ph-star"></i> {{ $product->gem_premium_score }}/100 - Premium Match</span>
                        @elseif($product->gem_premium_score >= 50)
                        <span class="badge" style="background: rgba(245,158,11,0.1); color: var(--warning); border: 1px solid rgba(245,158,11,0.2); font-size: 0.9rem;"><i class="ph-fill ph-star-half"></i> {{ $product->gem_premium_score }}/100 - Average</span>
                        @else
                        <span class="badge badge-danger" style="font-size: 0.9rem;"><i class="ph-fill ph-warning"></i> {{ $product->gem_premium_score }}/100 - Poor Metric</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- 90-Day Price History Chart -->
    @if(count($labels) > 0)
    <div class="card mb-8" style="padding: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
            <h3 class="font-display" style="font-size: 1.25rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph-fill ph-chart-line-up" style="color: #A78BFA;"></i> 90-Day Price Trajectory
            </h3>
            <span class="badge badge-purple">Live Data</span>
        </div>
        <div style="height: 350px; position: relative;">
            <canvas id="priceChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Similar Products -->
    @if(isset($similar) && $similar->count() > 0)
    <div class="mb-8">
        <h2 class="font-display mb-4" style="font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
            <i class="ph ph-squares-four" style="color: var(--secondary);"></i> Related Assets
        </h2>
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
            @foreach($similar as $sim)
            <div class="card" style="padding: 1.5rem;">
                <div style="font-size: 0.75rem; color: var(--secondary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 0.5rem;">{{ $sim->category }}</div>
                <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; line-height: 1.4;">{{ Str::limit($sim->name, 50) }}</h4>
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    @if($sim->gem_price)
                    <div><span style="font-size: 0.7rem; color: var(--gem); font-weight: 700;">GEM</span><br><span style="font-weight: 700;">₹{{ number_format($sim->gem_price, 0) }}</span></div>
                    @endif
                    @if($sim->amazon_price)
                    <div><span style="font-size: 0.7rem; color: var(--amazon); font-weight: 700;">AMZ</span><br><span style="font-weight: 700;">₹{{ number_format($sim->amazon_price, 0) }}</span></div>
                    @endif
                </div>
                <a href="{{ route('product.show', $sim) }}" class="btn btn-outline" style="width: 100%; font-size: 0.85rem;">Analyze <i class="ph ph-arrow-right"></i></a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@section('scripts')
@if(count($labels) > 0)
<script>
    const ctx = document.getElementById('priceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'GeM',
                    data: @json($gemData),
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245,158,11,0.1)',
                    borderWidth: 2, tension: 0.4, fill: true,
                    pointBackgroundColor: '#F59E0B', pointRadius: 0, pointHoverRadius: 5,
                },
                {
                    label: 'Amazon',
                    data: @json($amazonData),
                    borderColor: '#FF9900',
                    backgroundColor: 'rgba(255,153,0,0.05)',
                    borderWidth: 2, tension: 0.4, fill: true,
                    pointBackgroundColor: '#FF9900', pointRadius: 0, pointHoverRadius: 5,
                },
                {
                    label: 'Flipkart',
                    data: @json($flipkartData),
                    borderColor: '#2874F0',
                    backgroundColor: 'rgba(40,116,240,0.05)',
                    borderWidth: 2, tension: 0.4, fill: true,
                    pointBackgroundColor: '#2874F0', pointRadius: 0, pointHoverRadius: 5,
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { labels: { color: '#94A3B8', font: { family: "'Plus Jakarta Sans'" }, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: { backgroundColor: 'rgba(3,0,20,0.9)', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1, titleColor: '#F8FAFC', bodyColor: '#94A3B8', padding: 12, cornerRadius: 8 }
            },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748B', font: { size: 10 }, maxTicksLimit: 12 } },
                y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748B', callback: v => '₹' + v.toLocaleString('en-IN') } }
            }
        }
    });
</script>
@endif
@endsection

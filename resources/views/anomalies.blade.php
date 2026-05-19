@extends('layouts.app')
@section('title', 'Threat Detection — NexBuy')

@section('content')
<div class="fade-up">
    <div class="mb-8">
        <h1 class="font-display" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="ph-fill ph-shield-warning text-gradient"></i> Threat Detection
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 800px;">
            AI-powered pattern recognition for excessive markup, cartel pricing behaviors, and counterfeit risk vectors across procurement channels.
        </p>
    </div>

    <!-- Telemetry Cards -->
    <div class="grid grid-3 mb-8">
        <div class="card" style="padding: 2rem; background: linear-gradient(135deg, rgba(239,68,68,0.1), transparent); border: 1px solid rgba(239,68,68,0.2);">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Critical Flags</div>
                    <div class="font-display" style="font-size: 3rem; font-weight: 800; color: var(--danger); line-height: 1;">{{ $products->count() }}</div>
                </div>
                <div style="width: 48px; height: 48px; background: rgba(239,68,68,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--danger); font-size: 1.5rem;">
                    <i class="ph-fill ph-warning-octagon"></i>
                </div>
            </div>
            <div style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-muted);">GeM listings exceeding market bounds</div>
        </div>
        
        <div class="card" style="padding: 2rem; background: linear-gradient(135deg, rgba(245,158,11,0.1), transparent); border: 1px solid rgba(245,158,11,0.2);">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Verification Required</div>
                    <div class="font-display" style="font-size: 3rem; font-weight: 800; color: var(--warning); line-height: 1;">{{ $counterfeitRisk->count() }}</div>
                </div>
                <div style="width: 48px; height: 48px; background: rgba(245,158,11,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--warning); font-size: 1.5rem;">
                    <i class="ph-fill ph-detective"></i>
                </div>
            </div>
            <div style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-muted);">Suspiciously low commercial pricing</div>
        </div>

        <div class="card" style="padding: 2rem; background: linear-gradient(135deg, rgba(16,185,129,0.1), transparent); border: 1px solid rgba(16,185,129,0.2);">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Cleared Vectors</div>
                    <div class="font-display" style="font-size: 3rem; font-weight: 800; color: var(--accent); line-height: 1;">{{ \App\Models\Product::count() - $products->count() - $counterfeitRisk->count() }}</div>
                </div>
                <div style="width: 48px; height: 48px; background: rgba(16,185,129,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1.5rem;">
                    <i class="ph-fill ph-shield-check"></i>
                </div>
            </div>
            <div style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-muted);">Normal pricing parameters established</div>
        </div>
    </div>

    <!-- Data Table 1: Overpriced -->
    <div class="mb-8">
        <h2 class="font-display mb-4" style="font-size: 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            <i class="ph-fill ph-trend-up" style="color: var(--danger);"></i> Severe Markup Detected (>30% Delta)
        </h2>
        
        @if($products->count() > 0)
        <div class="card" style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--glass-border); background: rgba(255,255,255,0.02);">
                        <th style="padding: 1.2rem 1.5rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Asset Identity</th>
                        <th style="padding: 1.2rem 1.5rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">GeM Vector</th>
                        <th style="padding: 1.2rem 1.5rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Market Baseline</th>
                        <th style="padding: 1.2rem 1.5rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Delta Risk</th>
                        <th style="padding: 1.2rem 1.5rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Excess Cost</th>
                        <th style="padding: 1.2rem 1.5rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                    @php
                        $marketPrices = array_filter([$p->amazon_price, $p->flipkart_price]);
                        $marketAvg    = count($marketPrices) ? array_sum($marketPrices) / count($marketPrices) : 0;
                        $pct          = $marketAvg > 0 ? round((($p->gem_price / $marketAvg) - 1) * 100, 1) : 0;
                        $overcharge   = $p->gem_price - $marketAvg;
                    @endphp
                    <tr style="border-bottom: 1px solid var(--glass-border); transition: var(--transition);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 1rem 1.5rem;">
                            <div style="font-weight: 600; font-size: 0.95rem; color: white; margin-bottom: 0.2rem;">{{ Str::limit($p->name, 40) }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $p->brand }} · {{ $p->category }}</div>
                        </td>
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: var(--gem);">₹{{ number_format($p->gem_price, 0) }}</td>
                        <td style="padding: 1rem 1.5rem; font-weight: 600; color: var(--accent);">₹{{ number_format($marketAvg, 0) }}</td>
                        <td style="padding: 1rem 1.5rem;">
                            <span style="background: rgba(239,68,68,0.15); color: var(--danger); padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.85rem; font-weight: 700; border: 1px solid rgba(239,68,68,0.3);">
                                <i class="ph ph-arrow-up"></i> {{ $pct }}%
                            </span>
                        </td>
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: var(--danger);">₹{{ number_format($overcharge, 0) }}</td>
                        <td style="padding: 1rem 1.5rem; text-align: right;">
                            <a href="{{ route('product.show', $p) }}" class="btn btn-ghost" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Inspect</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="card" style="padding: 3rem; text-align: center; border: 1px dashed rgba(16,185,129,0.3); background: rgba(16,185,129,0.02);">
            <i class="ph-fill ph-check-circle" style="font-size: 3rem; color: var(--accent); margin-bottom: 1rem;"></i>
            <h3 class="font-display" style="font-size: 1.25rem; font-weight: 600;">System Nominal</h3>
            <p style="color: var(--text-muted);">No significant markup anomalies detected across indexed vectors.</p>
        </div>
        @endif
    </div>

    <!-- Knowledge Base -->
    <div class="card" style="padding: 2.5rem; background: rgba(0,0,0,0.3);">
        <h3 class="font-display" style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
            <i class="ph-fill ph-cpu"></i> Neural Analysis Metrics
        </h3>
        <div class="grid grid-2">
            <div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: #A78BFA; margin-bottom: 0.5rem;">GeM Markup Heuristics</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">Algorithms flag SKUs where GeM listed prices violate a 30% upper-bound threshold relative to the moving average of commercial counterparts (Amazon/Flipkart). This identifies predatory pricing patterns for government buyers.</p>
            </div>
            <div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--warning); margin-bottom: 0.5rem;">Counterfeit Probability</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">Commercial listings exhibiting a >40% negative deviation from median network pricing trigger a verification flag. This delta often correlates with gray-market hardware or unauthorized distributor channels.</p>
            </div>
        </div>
    </div>
</div>
@endsection

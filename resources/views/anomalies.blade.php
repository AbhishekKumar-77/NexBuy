@extends('layouts.app')
@section('title', 'Fraud & Anomaly Alerts — NexBuy')

@section('content')
<div class="main">
    <div class="page-title">🚨 Price Anomaly & Fraud Alerts</div>
    <p class="text-muted mb-4">ML-powered detection of overpriced GeM listings, cartel-like pricing, and counterfeit risk on commercial platforms.</p>

    <!-- Alert Summary -->
    <div class="grid-3 mb-4">
        <div class="card" style="padding:1.25rem;background:linear-gradient(135deg,rgba(239,68,68,0.1),rgba(239,68,68,0.03));">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="font-size:2rem;">⚠️</div>
                <div>
                    <div style="font-size:1.75rem;font-weight:800;font-family:'Outfit',sans-serif;color:var(--danger);">{{ $products->count() }}</div>
                    <div class="text-sm text-muted">Overpriced on GeM</div>
                </div>
            </div>
        </div>
        <div class="card" style="padding:1.25rem;background:linear-gradient(135deg,rgba(245,158,11,0.1),rgba(245,158,11,0.03));">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="font-size:2rem;">🕵️</div>
                <div>
                    <div style="font-size:1.75rem;font-weight:800;font-family:'Outfit',sans-serif;color:var(--warning);">{{ $counterfeitRisk->count() }}</div>
                    <div class="text-sm text-muted">Counterfeit Risk Flag</div>
                </div>
            </div>
        </div>
        <div class="card" style="padding:1.25rem;background:linear-gradient(135deg,rgba(34,197,94,0.1),rgba(34,197,94,0.03));">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="font-size:2rem;">✅</div>
                <div>
                    <div style="font-size:1.75rem;font-weight:800;font-family:'Outfit',sans-serif;color:var(--success);">
                        {{ \App\Models\Product::count() - $products->count() - $counterfeitRisk->count() }}
                    </div>
                    <div class="text-sm text-muted">Products Look Fine</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overpriced GeM Listings -->
    <div class="mb-4">
        <div class="section-title">
            <div class="icon" style="background:linear-gradient(135deg,var(--danger),#c0392b);">⚠️</div>
            Overpriced GeM Listings (>30% above market average)
        </div>

        @if($products->count() > 0)
        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>GeM Price</th>
                            <th>Market Avg</th>
                            <th>Overprice %</th>
                            <th>Potential Overcharge</th>
                            <th>Action</th>
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
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:0.88rem;">{{ Str::limit($p->name, 50) }}</div>
                                <div class="text-xs text-muted">{{ $p->category }} · {{ $p->brand }}</div>
                            </td>
                            <td>
                                <span style="color:var(--gem);font-weight:700;">₹{{ number_format($p->gem_price, 0) }}</span>
                            </td>
                            <td>
                                <span style="color:var(--success);font-weight:600;">₹{{ number_format($marketAvg, 0) }}</span>
                            </td>
                            <td>
                                <span style="background:rgba(239,68,68,0.15);color:var(--danger);padding:0.2rem 0.6rem;border-radius:50px;font-size:0.8rem;font-weight:700;">
                                    +{{ $pct }}%
                                </span>
                            </td>
                            <td>
                                <span style="color:var(--danger);font-weight:700;">₹{{ number_format($overcharge, 0) }}/unit</span>
                            </td>
                            <td>
                                <a href="{{ route('product.show', $p) }}" class="btn btn-ghost btn-sm">View →</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div style="background:rgba(34,197,94,0.06);border:1px solid rgba(34,197,94,0.25);border-radius:var(--radius);padding:1.5rem;text-align:center;">
            <div style="font-size:2rem;margin-bottom:0.5rem;">✅</div>
            <p style="color:var(--success);font-weight:600;">No overpriced GeM listings detected!</p>
        </div>
        @endif
    </div>

    <!-- Counterfeit Risk -->
    <div class="mb-4">
        <div class="section-title">
            <div class="icon" style="background:linear-gradient(135deg,var(--warning),#c87f00);">🕵️</div>
            Potential Counterfeit Risk (Suspiciously Low Amazon Price)
        </div>

        @if($counterfeitRisk->count() > 0)
        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Amazon Price</th>
                            <th>Other Platforms Avg</th>
                            <th>Discount %</th>
                            <th>Risk Level</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($counterfeitRisk as $p)
                        @php
                            $otherPrices = array_filter([$p->gem_price, $p->flipkart_price]);
                            $otherAvg    = count($otherPrices) ? array_sum($otherPrices) / count($otherPrices) : 0;
                            $discPct     = $otherAvg > 0 ? round((1 - ($p->amazon_price / $otherAvg)) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:0.88rem;">{{ Str::limit($p->name, 50) }}</div>
                                <div class="text-xs text-muted">{{ $p->category }}</div>
                            </td>
                            <td>
                                <span style="color:var(--amazon);font-weight:700;">₹{{ number_format($p->amazon_price, 0) }}</span>
                            </td>
                            <td>
                                <span style="font-weight:600;">₹{{ number_format($otherAvg, 0) }}</span>
                            </td>
                            <td>
                                <span style="background:rgba(245,158,11,0.15);color:var(--warning);padding:0.2rem 0.6rem;border-radius:50px;font-size:0.8rem;font-weight:700;">
                                    -{{ $discPct }}%
                                </span>
                            </td>
                            <td>
                                @if($discPct > 50)
                                <span class="badge badge-danger">🔴 High Risk</span>
                                @else
                                <span class="badge" style="background:rgba(245,158,11,0.1);color:var(--warning);border:1px solid rgba(245,158,11,0.3);">🟡 Medium</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('product.show', $p) }}" class="btn btn-ghost btn-sm">View →</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div style="background:rgba(34,197,94,0.06);border:1px solid rgba(34,197,94,0.25);border-radius:var(--radius);padding:1.5rem;text-align:center;">
            <div style="font-size:2rem;margin-bottom:0.5rem;">✅</div>
            <p style="color:var(--success);font-weight:600;">No suspicious pricing patterns detected!</p>
        </div>
        @endif
    </div>

    <!-- How it works -->
    <div style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2rem;">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1.2rem;font-weight:700;margin-bottom:1rem;">🧠 How Anomaly Detection Works</h2>
        <div class="grid-2">
            <div>
                <h3 style="font-size:0.9rem;font-weight:700;color:var(--danger);margin-bottom:0.4rem;">⚠️ GeM Overpricing Flag</h3>
                <p class="text-sm text-muted">If a GeM listing is priced <strong style="color:var(--text)">more than 30% above the average commercial price</strong> (Amazon + Flipkart), it's flagged as a potential overpricing case. This helps procurement officers avoid paying a premium without justification.</p>
            </div>
            <div>
                <h3 style="font-size:0.9rem;font-weight:700;color:var(--warning);margin-bottom:0.4rem;">🕵️ Counterfeit Risk Flag</h3>
                <p class="text-sm text-muted">If a product on Amazon is priced <strong style="color:var(--text)">more than 40% below</strong> the average of GeM + Flipkart prices, it may indicate a counterfeit or unauthorized listing. Buyers should verify seller credentials before purchasing.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'TCO Calculator — NexBuy')

@section('content')
<div class="main">
    <div class="page-title">💰 Total Cost of Ownership Calculator</div>
    <p class="text-muted mb-4">Goes beyond the sticker price — includes GST, shipping, Annual Maintenance Costs, and warranty gaps.</p>

    <div class="grid-2" style="gap:2rem;align-items:start;">

        <!-- INPUT FORM -->
        <div class="card fade-in">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--border);">
                <h2 style="font-size:1rem;font-weight:700;">⚙️ Configure Calculation</h2>
            </div>
            <div style="padding:1.5rem;">
                <form method="GET" action="{{ route('tco') }}">
                    <div class="form-group mb-3">
                        <label class="form-label">Select Product</label>
                        <select name="product_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">-- Choose a product --</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>
                                {{ $p->name }} @if($p->brand)— {{ $p->brand }}@endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    @if($product)
                    <div class="grid-2 mb-3" style="gap:1rem;">
                        <div class="form-group">
                            <label class="form-label">Quantity (units)</label>
                            <input type="number" name="quantity" class="form-input" min="1" max="10000"
                                   value="{{ request('quantity', 1) }}" placeholder="1"/>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ownership Period (years)</label>
                            <input type="number" name="years" class="form-input" min="1" max="10"
                                   value="{{ request('years', 3) }}" placeholder="3"/>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">Calculate TCO →</button>
                    @endif
                </form>

                @if($product)
                <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--border);">
                    <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.75rem;">TCO Includes</div>
                    @php
                        $tcoItems = [
                            ['💵', 'Base Price', 'Unit price × quantity'],
                            ['🏛️', 'GST (' . $product->gst_percent . '%)', 'Applicable GST on base total'],
                            ['🚚', 'Shipping', 'Per-unit delivery charges'],
                            ['🔧', 'AMC (8%/yr)', 'Annual maintenance after warranty expires'],
                        ];
                    @endphp
                    @foreach($tcoItems as [$icon, $label, $desc])
                    <div style="display:flex;gap:0.75rem;align-items:start;margin-bottom:0.6rem;">
                        <span style="font-size:1rem;">{{ $icon }}</span>
                        <div>
                            <div style="font-size:0.85rem;font-weight:600;">{{ $label }}</div>
                            <div style="font-size:0.75rem;color:var(--text-muted);">{{ $desc }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- RESULTS -->
        @if($product && count($results))
        <div class="fade-in">
            <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem;">
                📊 TCO Results — {{ Str::limit($product->name, 50) }}
            </h2>
            <p class="text-muted text-sm mb-3">
                Quantity: <strong style="color:var(--text)">{{ request('quantity', 1) }} units</strong> ·
                Period: <strong style="color:var(--text)">{{ request('years', 3) }} years</strong>
            </p>

            @php
                $minTotal = min(array_column($results, 'total'));
            @endphp

            @foreach($results as $platform => $tco)
            @php
                $colors = ['gem' => 'gem', 'amazon' => 'amazon', 'flipkart' => 'flipkart', 'indiamart' => 'indiamart'];
                $names  = ['gem' => 'GeM Portal', 'amazon' => 'Amazon', 'flipkart' => 'Flipkart', 'indiamart' => 'IndiaMART'];
                $isBest = $tco['total'] == $minTotal;
            @endphp
            <div class="card mb-2" style="{{ $isBest ? 'border-color:rgba(0,212,170,0.4);background:rgba(0,212,170,0.03);' : '' }}">
                <div style="padding:1rem 1.25rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="platform-logo {{ $platform }}" style="width:40px;height:40px;font-size:0.6rem;">
                                {{ strtoupper(substr($names[$platform], 0, 3)) }}
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:0.95rem;">{{ $names[$platform] }}</div>
                                @if($isBest)<span class="badge badge-accent" style="font-size:0.65rem;">✅ Lowest TCO</span>@endif
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:1.6rem;font-weight:900;font-family:'Outfit',sans-serif;color:{{ $isBest ? 'var(--accent)' : 'var(--text)' }};">
                                ₹{{ number_format($tco['total'], 0) }}
                            </div>
                            <div class="text-xs text-muted">Total Ownership Cost</div>
                        </div>
                    </div>

                    <!-- Breakdown bars -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;font-size:0.8rem;">
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:0.25rem;">
                                <span class="text-muted">Base Price</span>
                                <span class="font-semibold">₹{{ number_format($tco['base_total'], 0) }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:{{ round(($tco['base_total']/$tco['total'])*100) }}%;background:var(--primary);"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:0.25rem;">
                                <span class="text-muted">GST ({{ $product->gst_percent }}%)</span>
                                <span class="font-semibold">₹{{ number_format($tco['gst'], 0) }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:{{ round(($tco['gst']/$tco['total'])*100) }}%;background:var(--warning);"></div>
                            </div>
                        </div>
                        @if($tco['shipping'] > 0)
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:0.25rem;">
                                <span class="text-muted">Shipping</span>
                                <span class="font-semibold">₹{{ number_format($tco['shipping'], 0) }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:{{ round(($tco['shipping']/$tco['total'])*100) }}%;background:var(--amazon);"></div>
                            </div>
                        </div>
                        @endif
                        @if($tco['amc'] > 0)
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:0.25rem;">
                                <span class="text-muted">AMC (Post-warranty)</span>
                                <span class="font-semibold">₹{{ number_format($tco['amc'], 0) }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:{{ round(($tco['amc']/$tco['total'])*100) }}%;background:var(--danger);"></div>
                            </div>
                        </div>
                        @endif
                        @if(isset($tco['advanced_total']))
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:0.25rem;">
                                <span class="text-muted">Lifecycle Energy Cost</span>
                                <span class="font-semibold" style="color:var(--danger);">+₹{{ number_format($tco['energy_cost'], 0) }}</span>
                            </div>
                        </div>
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:0.25rem;">
                                <span class="text-muted">Est. Salvage Value</span>
                                <span class="font-semibold" style="color:var(--success);">-₹{{ number_format($tco['salvage_value'], 0) }}</span>
                            </div>
                        </div>
                        <div style="grid-column: 1 / -1; margin-top: 10px; padding: 10px; background: rgba(108,99,255,0.1); border-radius: 6px; text-align: center; font-weight: bold;">
                            Advanced Total Ownership Cost: <span style="color: var(--primary);">₹{{ number_format($tco['advanced_total'], 0) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Generate CS Report -->
            <a href="{{ route('cs.report', ['ids' => $product->id, 'quantity' => request('quantity', 1)]) }}"
               class="btn btn-primary w-full mt-2" style="justify-content:center;">
                📋 Generate Comparative Statement Report
            </a>
        </div>

        @elseif(!$product)
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:4rem 2rem;text-align:center;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);">
            <div style="font-size:4rem;margin-bottom:1rem;">💰</div>
            <h3 style="font-size:1.25rem;font-weight:700;margin-bottom:0.5rem;">Select a Product to Start</h3>
            <p class="text-muted text-sm">Choose a product from the dropdown on the left to see full TCO breakdown across all platforms.</p>
        </div>
        @endif
    </div>

    <!-- INFO SECTION -->
    <div style="margin-top:3rem;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2rem;">
        <h2 class="section-title" style="font-size:1.2rem;">❓ What is TCO and Why Does It Matter?</h2>
        <div class="grid-3">
            <div>
                <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:0.5rem;color:var(--accent);">Beyond Sticker Price</h3>
                <p class="text-sm text-muted">A product listed at ₹1,000 on GeM and ₹900 on Amazon may actually cost more on Amazon once GST differences, shipping, and shorter warranty periods are factored in.</p>
            </div>
            <div>
                <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:0.5rem;color:var(--primary);">Compliance Savings</h3>
                <p class="text-sm text-muted">Products without BIS certification may seem cheaper but expose your department to audit risk and replacement costs — hidden costs that TCO surfaces.</p>
            </div>
            <div>
                <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:0.5rem;color:var(--gem);">Bulk Procurement</h3>
                <p class="text-sm text-muted">For government bulk purchases (50+ units), even small per-unit differences compound into lakhs. TCO helps you justify the right procurement decision with data.</p>
            </div>
        </div>
    </div>
</div>
@endsection

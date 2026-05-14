<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Comparative Statement — NexBuy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; color: #1a202c; }

        .no-print-bar {
            background: #1a1a2e; padding: 0.75rem 2rem;
            display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
        }
        .no-print-bar a { color: #8888a8; text-decoration: none; font-size: 0.85rem; }
        .no-print-bar .btn-print {
            background: #6C63FF; color: white; border: none; padding: 0.5rem 1.5rem;
            border-radius: 50px; cursor: pointer; font-size: 0.875rem; font-weight: 600;
            font-family: 'Inter', sans-serif; margin-left: auto;
        }
        .no-print-bar .btn-print:hover { background: #574fd6; }

        .report-wrap { max-width: 1100px; margin: 2rem auto; padding: 0 1rem 3rem; }

        .report-paper {
            background: white; border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* ── HEADER ── */
        .report-header {
            background: linear-gradient(135deg, #1a1a2e, #0f0f23);
            color: white; padding: 2rem 2.5rem;
        }
        .report-header-top { display: flex; align-items: start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .govt-emblem { display: flex; align-items: center; gap: 1rem; }
        .emblem-circle {
            width: 64px; height: 64px; border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
        }
        .govt-title h1 { font-size: 1rem; font-weight: 700; color: rgba(255,255,255,0.9); }
        .govt-title p { font-size: 0.8rem; color: rgba(255,255,255,0.5); }
        .cs-title { text-align: right; }
        .cs-title h2 { font-size: 1.4rem; font-weight: 800; color: #f59e0b; }
        .cs-title p { font-size: 0.75rem; color: rgba(255,255,255,0.5); margin-top: 0.25rem; }

        /* ── META ── */
        .report-meta { background: #f7fafc; border-bottom: 2px solid #e2e8f0; padding: 1.25rem 2.5rem; }
        .meta-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        .meta-item label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px; color: #718096; font-weight: 600; }
        .meta-item input {
            display: block; border: none; background: transparent; font-size: 0.9rem;
            font-weight: 700; color: #2d3748; font-family: 'Inter', sans-serif;
            width: 100%; padding: 0.2rem 0; border-bottom: 1px dashed #cbd5e0;
            outline: none;
        }
        .meta-item input:focus { border-bottom-color: #6C63FF; }

        /* ── TABLE ── */
        .report-body { padding: 1.5rem 2.5rem; }
        .section-label {
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.5px; color: #6C63FF; margin-bottom: 0.75rem;
            padding-bottom: 0.4rem; border-bottom: 2px solid #6C63FF;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; font-size: 0.82rem; }
        thead { background: #2d3748; color: white; }
        thead th { padding: 0.65rem 0.85rem; text-align: left; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
        tbody tr:nth-child(even) { background: #f7fafc; }
        tbody td { padding: 0.75rem 0.85rem; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        tbody tr:hover { background: #ebf8ff; }
        .platform-cell { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 700; }
        .gem-cell { background: #fef3c7; color: #92400e; }
        .amazon-cell { background: #fff3cd; color: #92400e; }
        .flipkart-cell { background: #dbeafe; color: #1e40af; }
        .indiamart-cell { background: #fee2e2; color: #991b1b; }
        .best-highlight { background: #f0fff4 !important; font-weight: 700; color: #065f46; }
        .badge-small {
            display: inline-block; padding: 0.1rem 0.4rem; border-radius: 3px;
            font-size: 0.65rem; font-weight: 600;
        }
        .badge-bis { background: #d1fae5; color: #065f46; }
        .badge-mii { background: #dbeafe; color: #1e40af; }

        /* ── SUMMARY ── */
        .summary-section { background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; margin-bottom: 2rem; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
        .summary-item { text-align: center; }
        .summary-item .val { font-size: 1.5rem; font-weight: 800; color: #2d3748; font-family: 'Inter', sans-serif; }
        .summary-item .lbl { font-size: 0.7rem; color: #718096; text-transform: uppercase; letter-spacing: 0.4px; }

        /* ── RECOMMENDATION ── */
        .recommendation {
            background: linear-gradient(135deg, #f0fff4, #e6fffa);
            border: 2px solid #68d391; border-radius: 10px;
            padding: 1.25rem; margin-bottom: 2rem;
        }
        .recommendation h3 { color: #065f46; font-size: 0.95rem; font-weight: 800; margin-bottom: 0.4rem; }
        .recommendation p { color: #2d6a4f; font-size: 0.82rem; line-height: 1.5; }

        /* ── SIGNATURE BLOCK ── */
        .signature-block { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e2e8f0; }
        .sig-item { text-align: center; }
        .sig-line { height: 1px; background: #2d3748; margin-bottom: 0.4rem; }
        .sig-label { font-size: 0.72rem; color: #718096; }

        /* ── FOOTER ── */
        .report-footer {
            background: #2d3748; color: rgba(255,255,255,0.6); padding: 1rem 2.5rem;
            font-size: 0.72rem; text-align: center;
        }

        @media print {
            body { background: white; }
            .no-print-bar { display: none !important; }
            .report-wrap { margin: 0; padding: 0; max-width: 100%; }
            .report-paper { box-shadow: none; border-radius: 0; }
        }
    </style>
</head>
<body>

<!-- Print bar (hidden on print) -->
<div class="no-print-bar">
    <a href="{{ url()->previous() }}">← Back</a>
    <span style="color:#8888a8;font-size:0.85rem;">Comparative Statement Generator</span>
    <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
</div>

<div class="report-wrap">
    <div class="report-paper">

        <!-- Header -->
        <div class="report-header">
            <div class="report-header-top">
                <div class="govt-emblem">
                    <div class="emblem-circle">🏛️</div>
                    <div class="govt-title">
                        <h1>Government Procurement Statement</h1>
                        <p>GeM — Government e-Marketplace | NexBuy Price Intelligence</p>
                    </div>
                </div>
                <div class="cs-title">
                    <h2>COMPARATIVE STATEMENT</h2>
                    <p>Auto-generated on {{ now()->format('d M Y, H:i') }}</p>
                    <p style="margin-top:0.25rem;">Ref: NB/CS/{{ now()->format('Y') }}/{{ rand(1000,9999) }}</p>
                </div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="report-meta">
            <div class="meta-grid">
                <div class="meta-item">
                    <label>Department / Ministry</label>
                    <input type="text" value="{{ $dept }}" id="dept-field"/>
                </div>
                <div class="meta-item">
                    <label>Prepared By</label>
                    <input type="text" value="{{ $officer ?: 'Procurement Officer' }}" id="officer-field"/>
                </div>
                <div class="meta-item">
                    <label>Quantity (Units)</label>
                    <input type="text" value="{{ $quantity }}" readonly/>
                </div>
                <div class="meta-item">
                    <label>Date of Comparison</label>
                    <input type="text" value="{{ now()->format('d-m-Y') }}" readonly/>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="report-body">

            <!-- Summary -->
            <div class="section-label">EXECUTIVE SUMMARY</div>
            @php
                $bestPlatformCount = ['GeM' => 0, 'Amazon' => 0, 'Flipkart' => 0, 'IndiaMART' => 0];
                foreach($products as $p) {
                    $bp = $p->lowest_platform;
                    if (isset($bestPlatformCount[$bp])) $bestPlatformCount[$bp]++;
                }
                $overallBest = array_search(max($bestPlatformCount), $bestPlatformCount);
                $totalSavings = $products->sum(fn($p) => $p->savings) * $quantity;
            @endphp
            <div class="summary-section mb-3">
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="val">{{ $products->count() }}</div>
                        <div class="lbl">Products Compared</div>
                    </div>
                    <div class="summary-item">
                        <div class="val">{{ $overallBest }}</div>
                        <div class="lbl">Recommended Platform</div>
                    </div>
                    <div class="summary-item">
                        <div class="val">₹{{ number_format($totalSavings, 0) }}</div>
                        <div class="lbl">Est. Savings ({{ $quantity }} units)</div>
                    </div>
                </div>
            </div>

            <!-- Main Comparison Table -->
            <div class="section-label">PRODUCT-WISE PRICE COMPARISON</div>
            <table>
                <thead>
                    <tr>
                        <th style="width:5%">S.No</th>
                        <th style="width:25%">Product Description</th>
                        <th style="width:8%">Category</th>
                        <th style="width:10%">GeM Price</th>
                        <th style="width:10%">Amazon</th>
                        <th style="width:10%">Flipkart</th>
                        <th style="width:10%">IndiaMART</th>
                        <th style="width:10%">Lowest Price</th>
                        <th style="width:8%">Best Platform</th>
                        <th style="width:9%">Compliance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $i => $p)
                    <tr @if($p->lowest_platform === 'GeM') class="best-highlight" @endif>
                        <td style="font-weight:700;color:#6C63FF;">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <strong style="font-size:0.8rem;">{{ Str::limit($p->name, 55) }}</strong>
                            @if($p->brand)<br><span style="color:#718096;font-size:0.7rem;">{{ $p->brand }}</span>@endif
                        </td>
                        <td style="font-size:0.75rem;color:#718096;">{{ $p->category }}</td>
                        <td>
                            @if($p->gem_price)
                            <span style="@if($p->lowest_platform==='GeM')font-weight:800;color:#065f46;@endif">
                                ₹{{ number_format($p->gem_price, 2) }}
                            </span>
                            @else<span style="color:#cbd5e0;">N/A</span>@endif
                        </td>
                        <td>
                            @if($p->amazon_price)
                            <span style="@if($p->lowest_platform==='Amazon')font-weight:800;color:#065f46;@endif">
                                ₹{{ number_format($p->amazon_price, 2) }}
                            </span>
                            @else<span style="color:#cbd5e0;">N/A</span>@endif
                        </td>
                        <td>
                            @if($p->flipkart_price)
                            <span style="@if($p->lowest_platform==='Flipkart')font-weight:800;color:#065f46;@endif">
                                ₹{{ number_format($p->flipkart_price, 2) }}
                            </span>
                            @else<span style="color:#cbd5e0;">N/A</span>@endif
                        </td>
                        <td>
                            @if($p->indiamart_price)
                            <span style="@if($p->lowest_platform==='IndiaMART')font-weight:800;color:#065f46;@endif">
                                ₹{{ number_format($p->indiamart_price, 2) }}
                            </span>
                            @else<span style="color:#cbd5e0;">N/A</span>@endif
                        </td>
                        <td style="font-weight:800;color:#065f46;font-size:0.9rem;">₹{{ number_format($p->lowest_price, 2) }}</td>
                        <td>
                            <span class="badge-small @if($p->lowest_platform === 'GeM') gem-cell @elseif($p->lowest_platform === 'Amazon') amazon-cell @elseif($p->lowest_platform === 'Flipkart') flipkart-cell @else indiamart-cell @endif">
                                {{ $p->lowest_platform }}
                            </span>
                        </td>
                        <td>
                            @if($p->gem_bis_certified || $p->amazon_bis_certified)
                            <span class="badge-small badge-bis">BIS ✓</span>
                            @endif
                            @if($p->gem_make_in_india)
                            <span class="badge-small badge-mii">MII ✓</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- TCO Summary Table -->
            <div class="section-label">QUANTITY-WISE COST SUMMARY ({{ $quantity }} units)</div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Platform</th>
                        <th>Unit Price</th>
                        <th>Total ({{ $quantity }} qty)</th>
                        <th>GST</th>
                        <th>Shipping</th>
                        <th>Total + Tax</th>
                        <th>Savings</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                    @php $tco = $p->computeTco(strtolower($p->lowest_platform) === 'gem' ? 'gem' : (strtolower($p->lowest_platform) === 'amazon' ? 'amazon' : (strtolower($p->lowest_platform) === 'flipkart' ? 'flipkart' : 'indiamart')), $quantity); @endphp
                    <tr class="best-highlight">
                        <td style="font-size:0.78rem;font-weight:600;">{{ Str::limit($p->name, 45) }}</td>
                        <td><span class="badge-small @if($p->lowest_platform==='GeM') gem-cell @elseif($p->lowest_platform==='Amazon') amazon-cell @elseif($p->lowest_platform==='Flipkart') flipkart-cell @else indiamart-cell @endif">{{ $p->lowest_platform }}</span></td>
                        <td>₹{{ number_format($p->lowest_price, 2) }}</td>
                        <td>₹{{ number_format($tco['base_total'], 2) }}</td>
                        <td>₹{{ number_format($tco['gst'], 2) }}</td>
                        <td>₹{{ number_format($tco['shipping'], 2) }}</td>
                        <td style="font-weight:800;">₹{{ number_format($tco['total'], 2) }}</td>
                        <td style="color:#065f46;font-weight:700;">₹{{ number_format($p->savings * $quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Recommendation -->
            <div class="recommendation">
                <h3>✅ RECOMMENDATION</h3>
                <p>
                    Based on the comparative analysis of prices across GeM, Amazon, Flipkart, and IndiaMART,
                    it is recommended that the purchase of the above {{ $products->count() }} item(s) be made from
                    <strong>{{ $overallBest }}</strong>, which offers the best combination of competitive pricing,
                    warranty coverage, compliance certification, and delivery reliability.
                    The estimated savings by choosing the recommended platform over the highest-priced alternative
                    amounts to approximately <strong>₹{{ number_format($totalSavings, 0) }}</strong> for {{ $quantity }} unit(s).
                    All recommended products are BIS certified and comply with applicable procurement norms.
                </p>
            </div>

            <!-- Signature -->
            <div class="signature-block">
                <div class="sig-item">
                    <div style="height:50px;"></div>
                    <div class="sig-line"></div>
                    <div class="sig-label">Prepared By<br><strong style="color:#2d3748;" id="sig-officer">{{ $officer ?: 'Procurement Officer' }}</strong></div>
                </div>
                <div class="sig-item">
                    <div style="height:50px;"></div>
                    <div class="sig-line"></div>
                    <div class="sig-label">Verified By<br><strong style="color:#2d3748;">Finance Officer</strong></div>
                </div>
                <div class="sig-item">
                    <div style="height:50px;"></div>
                    <div class="sig-line"></div>
                    <div class="sig-label">Approved By<br><strong style="color:#2d3748;">HOD / Director</strong></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="report-footer">
            Generated by NexBuy Price Intelligence Platform &nbsp;|&nbsp; {{ config('app.url') }} &nbsp;|&nbsp;
            This document is system-generated for procurement reference. Data accuracy subject to live platform prices.
        </div>
    </div>
</div>

<script>
// Live update signature from meta fields
document.getElementById('officer-field')?.addEventListener('input', function() {
    const sigOfficer = document.getElementById('sig-officer');
    if (sigOfficer) sigOfficer.textContent = this.value || 'Procurement Officer';
});
</script>
</body>
</html>

@extends('layouts.app')
@section('title', 'Analytics Dashboard — NexBuy')

@section('content')
<div class="fade-up">
    <div class="mb-8">
        <h1 class="font-display" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="ph-fill ph-chart-pie-slice text-gradient"></i> Analytics Command Center
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem;">
            Real-time procurement intelligence aggregated across {{ $totalProducts }} indexed assets.
        </p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-4 mb-8">
        <div class="card" style="padding: 2rem; border-top: 3px solid #7C3AED;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Total Products</div>
                    <div class="font-display" style="font-size: 2.5rem; font-weight: 800; color: white; line-height: 1;">{{ $totalProducts }}</div>
                </div>
                <i class="ph-fill ph-cube" style="font-size: 2rem; color: rgba(124,58,237,0.3);"></i>
            </div>
        </div>
        <div class="card" style="padding: 2rem; border-top: 3px solid #10B981;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Total Savings</div>
                    <div class="font-display" style="font-size: 2.5rem; font-weight: 800; color: var(--accent); line-height: 1;">₹{{ number_format($totalSavings, 0) }}</div>
                </div>
                <i class="ph-fill ph-piggy-bank" style="font-size: 2rem; color: rgba(16,185,129,0.3);"></i>
            </div>
        </div>
        <div class="card" style="padding: 2rem; border-top: 3px solid #F59E0B;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">GeM Cheaper</div>
                    <div class="font-display" style="font-size: 2.5rem; font-weight: 800; color: var(--gem); line-height: 1;">{{ $gemCheaperCount }}</div>
                </div>
                <i class="ph-fill ph-bank" style="font-size: 2rem; color: rgba(245,158,11,0.3);"></i>
            </div>
            <div style="margin-top: 0.75rem; font-size: 0.85rem; color: var(--text-muted);">{{ $totalProducts > 0 ? round($gemCheaperCount / $totalProducts * 100) : 0 }}% of tracked products</div>
        </div>
        <div class="card" style="padding: 2rem; border-top: 3px solid #EF4444;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Anomaly Flags</div>
                    <div class="font-display" style="font-size: 2.5rem; font-weight: 800; color: var(--danger); line-height: 1;">{{ $anomalyCount }}</div>
                </div>
                <i class="ph-fill ph-warning-octagon" style="font-size: 2rem; color: rgba(239,68,68,0.3);"></i>
            </div>
            <div style="margin-top: 0.75rem;"><a href="{{ route('anomalies') }}" style="font-size: 0.85rem; color: var(--danger); text-decoration: none;">View Alerts →</a></div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-2 mb-8" style="gap: 2rem;">
        <!-- Platform Distribution Pie Chart -->
        <div class="card" style="padding: 2rem;">
            <h3 class="font-display" style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph-fill ph-chart-pie" style="color: #A78BFA;"></i> Best Price Distribution
            </h3>
            <div style="height: 300px; position: relative;">
                <canvas id="platformChart"></canvas>
            </div>
        </div>

        <!-- Category Savings Bar Chart -->
        <div class="card" style="padding: 2rem;">
            <h3 class="font-display" style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph-fill ph-chart-bar" style="color: var(--accent);"></i> Category-Wise Average Savings
            </h3>
            <div style="height: 300px; position: relative;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Price Health Index -->
    <div class="card mb-8" style="padding: 2rem;">
        <h3 class="font-display" style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="ph-fill ph-heartbeat" style="color: var(--danger);"></i> Price Health Index
        </h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
                        <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Category</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; text-align: right;">Avg GeM Price</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; text-align: right;">Avg Market Price</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; text-align: right;">Delta</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Verdict</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoryHealth as $ch)
                    <tr style="border-bottom: 1px solid var(--glass-border);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 1rem; font-weight: 600;">{{ $ch['category'] }}</td>
                        <td style="padding: 1rem; text-align: right; color: var(--gem); font-weight: 600;">₹{{ number_format($ch['avg_gem'], 0) }}</td>
                        <td style="padding: 1rem; text-align: right; font-weight: 600;">₹{{ number_format($ch['avg_market'], 0) }}</td>
                        <td style="padding: 1rem; text-align: right;">
                            @if($ch['delta'] > 0)
                            <span style="color: var(--danger); font-weight: 700;">+{{ $ch['delta'] }}%</span>
                            @else
                            <span style="color: var(--accent); font-weight: 700;">{{ $ch['delta'] }}%</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            @if($ch['delta'] <= 0)
                            <span class="badge badge-success">GeM Optimal</span>
                            @elseif($ch['delta'] <= 10)
                            <span class="badge" style="background: rgba(245,158,11,0.1); color: var(--warning); border: 1px solid rgba(245,158,11,0.2);">Acceptable</span>
                            @else
                            <span class="badge badge-danger">Overpriced</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-3">
        <a href="{{ route('search') }}" class="card" style="padding: 2rem; text-decoration: none; text-align: center;">
            <i class="ph-fill ph-magnifying-glass" style="font-size: 2.5rem; color: #A78BFA; margin-bottom: 1rem;"></i>
            <h3 class="font-display" style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem; color: white;">Intelligence Search</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem;">Browse & compare all indexed products</p>
        </a>
        <a href="{{ route('tco') }}" class="card" style="padding: 2rem; text-decoration: none; text-align: center;">
            <i class="ph-fill ph-calculator" style="font-size: 2.5rem; color: var(--accent); margin-bottom: 1rem;"></i>
            <h3 class="font-display" style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem; color: white;">TCO Calculator</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem;">Compute true ownership cost</p>
        </a>
        <a href="{{ route('anomalies') }}" class="card" style="padding: 2rem; text-decoration: none; text-align: center;">
            <i class="ph-fill ph-shield-warning" style="font-size: 2.5rem; color: var(--danger); margin-bottom: 1rem;"></i>
            <h3 class="font-display" style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem; color: white;">Threat Scanner</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem;">Detect pricing anomalies & fraud</p>
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Platform Distribution Pie
    new Chart(document.getElementById('platformChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($platformDistribution)),
            datasets: [{
                data: @json(array_values($platformDistribution)),
                backgroundColor: ['#F59E0B', '#FF9900', '#2874F0', '#E63946'],
                borderColor: '#030014', borderWidth: 3, hoverOffset: 8
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { color: '#94A3B8', padding: 16, font: { family: "'Plus Jakarta Sans'" }, usePointStyle: true } }
            }
        }
    });

    // Category Savings Bar
    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: @json($categorySavings->pluck('category')),
            datasets: [{
                label: 'Avg Savings (₹)',
                data: @json($categorySavings->pluck('avg_savings')),
                backgroundColor: [
                    'rgba(124,58,237,0.6)', 'rgba(6,182,212,0.6)', 'rgba(16,185,129,0.6)',
                    'rgba(245,158,11,0.6)', 'rgba(239,68,68,0.6)', 'rgba(40,116,240,0.6)'
                ],
                borderRadius: 8, borderSkipped: false
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: 'rgba(3,0,20,0.9)', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1, padding: 12, cornerRadius: 8 }
            },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748B', callback: v => '₹' + v.toLocaleString('en-IN') } },
                y: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 11 } } }
            }
        }
    });
</script>
@endsection

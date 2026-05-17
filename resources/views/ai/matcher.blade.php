@extends('layouts.app')

@section('title', 'AI Product Matcher — NexBuy')

@section('content')
<div class="main fade-in">
    <h1 class="page-title" style="display:flex; align-items:center; gap:0.5rem;">
        🤖 AI Product Matcher
    </h1>
    <p class="text-muted mb-4">Leverage our LLM-powered engine to semantically compare product specifications and detect discrepancies between GeM and Commercial platforms.</p>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('ai.matcher') }}" method="GET" class="flex items-center gap-2">
                <div class="form-group flex-1" style="flex:1;">
                    <select name="product_id" class="form-select" required>
                        <option value="">-- Select a Product to Analyze --</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ isset($selectedProduct) && $selectedProduct->id == $prod->id ? 'selected' : '' }}>
                                {{ $prod->name }} ({{ $prod->brand }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Run Analysis</button>
            </form>
        </div>
    </div>

    @if(isset($analysisResult) && isset($selectedProduct))
    <div class="grid-2 mt-4">
        <!-- GeM vs Amazon Side-by-Side -->
        <div class="card">
            <div class="card-body">
                <h3 class="mb-2 platform-gem" style="font-weight: 700; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                    GeM Listing Details
                </h3>
                <h4 class="mt-3">{{ $selectedProduct->name }}</h4>
                <p class="text-muted text-sm mb-3">Brand: {{ $selectedProduct->brand }}</p>
                <div style="background: var(--bg3); padding: 1rem; border-radius: 8px;">
                    <pre style="white-space: pre-wrap; font-size: 0.85rem; color: #e8e8f0;">@json($selectedProduct->specifications, JSON_PRETTY_PRINT)</pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="background: rgba(108,99,255,0.05);">
                <h3 class="mb-2" style="font-weight: 700; color: var(--primary-light); border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                    LLM Semantic Analysis
                </h3>
                
                <div style="margin-top: 1.5rem; text-align: center;">
                    <div style="display: inline-block; padding: 2rem; border-radius: 50%; border: 4px solid {{ $analysisResult['is_match'] ? 'var(--success)' : 'var(--danger)' }}; font-size: 2rem; font-weight: bold; margin-bottom: 1rem;">
                        {{ str_replace('Match Score: ', '', $analysisResult['analysis'][0]) }}
                    </div>
                    <h4 class="{{ $analysisResult['is_match'] ? 'text-success' : 'text-danger' }}" style="color: {{ $analysisResult['is_match'] ? 'var(--success)' : 'var(--danger)' }}">
                        {{ $analysisResult['is_match'] ? 'High Confidence Match' : 'Low Confidence Match' }}
                    </h4>
                </div>

                <div class="mt-4">
                    <h5 class="mb-2 text-muted">Analysis Breakdown:</h5>
                    <ul style="list-style-type: none; padding-left: 0;">
                        @foreach(array_slice($analysisResult['analysis'], 1) as $line)
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--glass-border); font-size: 0.9rem;">
                                @if(str_contains($line, 'Exact Match'))
                                    <span style="color: var(--success);">✅</span>
                                @else
                                    <span style="color: var(--warning);">⚠️</span>
                                @endif
                                {{ $line }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-4 p-3" style="background: var(--bg3); border-radius: 8px; border-left: 4px solid var(--primary);">
                    <strong style="color: var(--primary-light);">Recommendation:</strong><br>
                    <span style="font-size: 0.95rem;">{{ $analysisResult['recommendation'] }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

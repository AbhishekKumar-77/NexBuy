@extends('layouts.app')
@section('title', 'AI Product Matcher — NexBuy')

@section('content')
<div class="fade-up">
    <div class="mb-8">
        <h1 class="font-display" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="ph-fill ph-brain text-gradient"></i> AI Product Matcher
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 700px;">
            LLM-powered semantic analysis engine that compares GeM and commercial product specifications to detect discrepancies and verify authenticity.
        </p>
    </div>

    <!-- Selection Card -->
    <div class="card mb-8" style="padding: 1.5rem;">
        <form action="{{ route('ai.matcher') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;">
                <label class="form-label">Select Asset for Deep Analysis</label>
                <select name="product_id" class="form-control" required>
                    <option value="">— Choose a product to analyze —</option>
                    @foreach($products as $prod)
                    <option value="{{ $prod->id }}" {{ isset($selectedProduct) && $selectedProduct->id == $prod->id ? 'selected' : '' }}>
                        {{ $prod->name }} ({{ $prod->brand }})
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="ph-fill ph-brain"></i> Run Neural Analysis</button>
        </form>
    </div>

    @if(isset($analysisResult) && isset($selectedProduct))
    <div class="grid grid-2 mb-8" style="gap: 2rem;">
        <!-- GeM Data Panel -->
        <div class="card" style="padding: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
                <div style="width: 40px; height: 40px; background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="ph-fill ph-bank" style="color: var(--gem); font-size: 1.2rem;"></i>
                </div>
                <h3 class="font-display" style="font-size: 1.2rem; font-weight: 600;">GeM Listing Profile</h3>
            </div>
            <h4 style="font-weight: 600; margin-bottom: 0.5rem;">{{ $selectedProduct->name }}</h4>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Brand: {{ $selectedProduct->brand }}</p>
            <div style="background: rgba(0,0,0,0.3); padding: 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--glass-border);">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.75rem; font-weight: 600;">Specification Vector</div>
                <pre style="white-space: pre-wrap; font-size: 0.85rem; color: #C4B5FD; font-family: 'Plus Jakarta Sans', monospace; line-height: 1.8;">@json($selectedProduct->specifications, JSON_PRETTY_PRINT)</pre>
            </div>
        </div>

        <!-- AI Analysis Panel -->
        <div class="card" style="padding: 2rem; background: linear-gradient(135deg, rgba(124,58,237,0.08), transparent); border: 1px solid rgba(124,58,237,0.2);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
                <div style="width: 40px; height: 40px; background: rgba(124,58,237,0.15); border: 1px solid rgba(124,58,237,0.3); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="ph-fill ph-cpu" style="color: #A78BFA; font-size: 1.2rem;"></i>
                </div>
                <h3 class="font-display" style="font-size: 1.2rem; font-weight: 600;">Neural Analysis Output</h3>
            </div>

            <!-- Score Display -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 100px; height: 100px; border-radius: 50%; border: 4px solid {{ $analysisResult['is_match'] ? 'var(--accent)' : 'var(--danger)' }}; background: rgba(0,0,0,0.3); backdrop-filter: blur(10px);">
                    <span class="font-display" style="font-size: 1.5rem; font-weight: 800; color: white;">
                        {{ str_replace('Match Score: ', '', $analysisResult['analysis'][0]) }}
                    </span>
                </div>
                <div style="margin-top: 1rem; font-weight: 700; font-size: 1.1rem; color: {{ $analysisResult['is_match'] ? 'var(--accent)' : 'var(--danger)' }};">
                    {{ $analysisResult['is_match'] ? '✅ High Confidence Match' : '⚠️ Low Confidence Match' }}
                </div>
            </div>

            <!-- Analysis Breakdown -->
            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem; font-weight: 600;">Analysis Breakdown</div>
                @foreach(array_slice($analysisResult['analysis'], 1) as $line)
                <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.9rem;">
                    @if(str_contains($line, 'Exact Match'))
                    <i class="ph-fill ph-check-circle" style="color: var(--accent); font-size: 1.2rem; flex-shrink: 0; margin-top: 2px;"></i>
                    @else
                    <i class="ph-fill ph-warning" style="color: var(--warning); font-size: 1.2rem; flex-shrink: 0; margin-top: 2px;"></i>
                    @endif
                    <span>{{ $line }}</span>
                </div>
                @endforeach
            </div>

            <!-- Recommendation -->
            <div style="background: rgba(0,0,0,0.3); padding: 1.25rem; border-radius: var(--radius-sm); border-left: 4px solid var(--primary);">
                <div style="font-size: 0.75rem; color: #A78BFA; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 0.5rem;">Intelligence Directive</div>
                <span style="font-size: 0.95rem; line-height: 1.6;">{{ $analysisResult['recommendation'] }}</span>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

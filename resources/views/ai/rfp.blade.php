@extends('layouts.app')
@section('title', 'AI RFP Generator — NexBuy')

@section('content')
<div class="fade-up">
    <div class="mb-8">
        <h1 class="font-display" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="ph-fill ph-file-text text-gradient"></i> AI RFP Generator
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 700px;">
            Automatically draft a government-standard Request for Proposal based on the exact specifications of compliant, cost-optimized products.
        </p>
    </div>

    <!-- Generator Form -->
    <div class="card mb-8" style="padding: 1.5rem;">
        <form action="{{ route('ai.rfp') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;">
                <label class="form-label">Base Product Specification</label>
                <select name="product_id" class="form-control" required>
                    <option value="">— Select a product for RFP specs —</option>
                    @foreach($products as $prod)
                    <option value="{{ $prod->id }}" {{ isset($selectedProduct) && $selectedProduct->id == $prod->id ? 'selected' : '' }}>
                        {{ $prod->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 250px;">
                <label class="form-label">Issuing Department</label>
                <input type="text" name="department" class="form-control" placeholder="Ministry of IT" value="{{ request('department') }}">
            </div>
            <button type="submit" class="btn btn-primary"><i class="ph-fill ph-magic-wand"></i> Generate RFP</button>
        </form>
    </div>

    @if(isset($generatedRfp))
    <!-- RFP Output -->
    <div class="card" style="overflow: hidden;">
        <div style="background: white; color: #1a1a2e; padding: 3rem;">
            {!! $generatedRfp !!}
        </div>
        <div style="padding: 1.25rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--glass-border); background: rgba(0,0,0,0.2);">
            <button class="btn btn-outline" onclick="navigator.clipboard.writeText(document.querySelector('.card div[style*=white]').innerText).then(() => this.innerHTML = '<i class=\'ph-fill ph-check\'></i> Copied!')">
                <i class="ph ph-copy"></i> Copy Text
            </button>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="ph-fill ph-printer"></i> Print / Export PDF
            </button>
        </div>
    </div>
    @endif
</div>
@endsection

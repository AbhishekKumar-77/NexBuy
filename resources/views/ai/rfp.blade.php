@extends('layouts.app')

@section('title', 'Automated RFP Generation — NexBuy')

@section('content')
<div class="main fade-in">
    <h1 class="page-title" style="display:flex; align-items:center; gap:0.5rem;">
        📄 AI RFP Generator
    </h1>
    <p class="text-muted mb-4">Automatically draft a Request for Proposal (RFP) based on the exact specifications of the most compliant or cost-effective product.</p>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('ai.rfp') }}" method="GET" class="flex items-center gap-2">
                <div class="form-group flex-1" style="flex:1;">
                    <select name="product_id" class="form-select" required>
                        <option value="">-- Select a Base Product for Specs --</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ isset($selectedProduct) && $selectedProduct->id == $prod->id ? 'selected' : '' }}>
                                {{ $prod->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="width: 300px;">
                    <input type="text" name="department" class="form-input" placeholder="Department Name" value="{{ request('department') }}">
                </div>
                <button type="submit" class="btn btn-primary">Generate RFP</button>
            </form>
        </div>
    </div>

    @if(isset($generatedRfp))
    <div class="card" style="background: white; color: black;">
        <div class="card-body" style="padding: 3rem;">
            {!! $generatedRfp !!}
        </div>
        <div class="card-body" style="border-top: 1px solid #ddd; background: #f9f9f9; text-align: right;">
            <button class="btn btn-primary" onclick="window.print()">🖨️ Print to PDF</button>
        </div>
    </div>
    @endif
</div>
@endsection

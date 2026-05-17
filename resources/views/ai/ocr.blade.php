@extends('layouts.app')

@section('title', 'OCR Quotation Analysis — NexBuy')

@section('content')
<div class="main fade-in">
    <h1 class="page-title" style="display:flex; align-items:center; gap:0.5rem;">
        📸 OCR Quotation Analyzer
    </h1>
    <p class="text-muted mb-4">Upload a scanned PDF or Image quotation from an offline vendor. Our AI will digitize the items and prepare them for GeM comparison.</p>

    <div class="grid-2">
        <!-- Upload Form -->
        <div class="card">
            <div class="card-body">
                <h3 class="mb-3" style="font-weight: 600;">Upload Vendor Quotation</h3>
                <form action="{{ route('ai.ocr') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label">Select File (PDF, PNG, JPG)</label>
                        <input type="file" name="quotation_file" class="form-input" style="padding: 1rem;" required>
                    </div>
                    <button type="submit" class="btn btn-accent w-full justify-center">Scan & Analyze Quotation</button>
                </form>
                
                <div class="mt-4 p-3" style="background: var(--bg3); border-radius: 8px;">
                    <p class="text-sm text-muted mb-0"><strong>Supported Formats:</strong> .pdf, .jpg, .png</p>
                    <p class="text-sm text-muted mt-1"><strong>Engine:</strong> Google Cloud Vision AI Integration</p>
                </div>
            </div>
        </div>

        <!-- Extraction Results -->
        @if(isset($extractedData))
        <div class="card" style="border-color: var(--success);">
            <div class="card-body">
                <h3 class="mb-2" style="font-weight: 700; color: var(--success); border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                    ✅ Extraction Successful
                </h3>
                
                <div class="mt-3 mb-4">
                    <p><strong>Vendor Name:</strong> {{ $extractedData['vendor_name'] }}</p>
                    <p><strong>Date:</strong> {{ $extractedData['quotation_date'] }}</p>
                </div>

                <div class="table-wrap mb-4">
                    <table>
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($extractedData['items'] as $item)
                            <tr>
                                <td>{{ $item['description'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>₹{{ number_format($item['unit_price']) }}</td>
                                <td>₹{{ number_format($item['total_price']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div style="text-align: right;">
                    <h4 style="color: var(--accent);">Estimated Grand Total: ₹{{ number_format($extractedData['grand_total']) }}</h4>
                    <p class="text-xs text-muted">(Includes GST)</p>
                </div>

                <div class="mt-4" style="text-align: right;">
                    <a href="{{ route('search') }}?q=Dell+Vostro" class="btn btn-primary">Compare on GeM Market</a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

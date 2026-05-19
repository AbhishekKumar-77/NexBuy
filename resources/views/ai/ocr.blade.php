@extends('layouts.app')
@section('title', 'OCR Quotation Analyzer — NexBuy')

@section('content')
<div class="fade-up">
    <div class="mb-8">
        <h1 class="font-display" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="ph-fill ph-scan text-gradient"></i> OCR Quotation Analyzer
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 700px;">
            Upload scanned vendor quotations. Our AI engine digitizes line items and prepares them for instant GeM price comparison.
        </p>
    </div>

    <div class="grid grid-2" style="gap: 2rem;">
        <!-- Upload Panel -->
        <div class="card" style="padding: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
                <div style="width: 40px; height: 40px; background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.3); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="ph-fill ph-upload-simple" style="color: var(--secondary); font-size: 1.2rem;"></i>
                </div>
                <h3 class="font-display" style="font-size: 1.15rem; font-weight: 600;">Upload Vendor Quotation</h3>
            </div>

            <form action="{{ route('ai.ocr') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Select Document (PDF, PNG, JPG)</label>
                    <div style="border: 2px dashed var(--glass-border); border-radius: var(--radius-md); padding: 3rem 2rem; text-align: center; background: rgba(0,0,0,0.2); transition: var(--transition); cursor: pointer;" id="dropZone"
                         onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--glass-border)'"
                         onclick="document.getElementById('fileInput').click()">
                        <i class="ph ph-cloud-arrow-up" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                        <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 0.5rem;">Click to browse or drag & drop</p>
                        <p style="color: var(--text-muted); font-size: 0.8rem;" id="fileName">Supports PDF, PNG, JPG up to 10MB</p>
                        <input type="file" name="quotation_file" id="fileInput" style="display: none;" required
                               onchange="document.getElementById('fileName').innerText = this.files[0]?.name || 'No file selected'">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ph-fill ph-scan"></i> Scan & Extract Data
                </button>
            </form>

            <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(0,0,0,0.2); border-radius: var(--radius-sm); border: 1px solid var(--glass-border);">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); font-weight: 600; margin-bottom: 0.5rem;">Engine Details</div>
                <div style="display: flex; gap: 1rem; font-size: 0.85rem;">
                    <span style="color: var(--text-muted);"><i class="ph ph-file-pdf"></i> PDF, JPG, PNG</span>
                    <span style="color: var(--text-muted);"><i class="ph ph-cpu"></i> Cloud Vision AI</span>
                </div>
            </div>
        </div>

        <!-- Extraction Results -->
        @if(isset($extractedData))
        <div class="card" style="padding: 2rem; border: 1px solid rgba(16,185,129,0.3); background: linear-gradient(135deg, rgba(16,185,129,0.05), transparent);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
                <div style="width: 40px; height: 40px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="ph-fill ph-check-circle" style="color: var(--accent); font-size: 1.2rem;"></i>
                </div>
                <h3 class="font-display" style="font-size: 1.15rem; font-weight: 600;">Extraction Complete</h3>
            </div>

            <div style="display: flex; gap: 2rem; margin-bottom: 1.5rem;">
                <div>
                    <div class="form-label">Vendor</div>
                    <div style="font-weight: 600; font-size: 1rem;">{{ $extractedData['vendor_name'] }}</div>
                </div>
                <div>
                    <div class="form-label">Date</div>
                    <div style="font-weight: 600; font-size: 1rem;">{{ $extractedData['quotation_date'] }}</div>
                </div>
            </div>

            <!-- Items Table -->
            <div style="overflow-x: auto; margin-bottom: 1.5rem; border: 1px solid var(--glass-border); border-radius: var(--radius-sm);">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: rgba(0,0,0,0.3); border-bottom: 1px solid var(--glass-border);">
                            <th style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Item Description</th>
                            <th style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Qty</th>
                            <th style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Unit Price</th>
                            <th style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($extractedData['items'] as $item)
                        <tr style="border-bottom: 1px solid var(--glass-border);">
                            <td style="padding: 0.85rem 1rem; font-weight: 500;">{{ $item['description'] }}</td>
                            <td style="padding: 0.85rem 1rem;">{{ $item['quantity'] }}</td>
                            <td style="padding: 0.85rem 1rem; font-weight: 600;">₹{{ number_format($item['unit_price']) }}</td>
                            <td style="padding: 0.85rem 1rem; font-weight: 600; color: white;">₹{{ number_format($item['total_price']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); border-radius: var(--radius-sm);">
                <span style="font-weight: 600; color: var(--text-muted);">Estimated Grand Total (incl. GST)</span>
                <span class="font-display" style="font-size: 1.5rem; font-weight: 800; color: var(--accent);">₹{{ number_format($extractedData['grand_total']) }}</span>
            </div>

            <div style="margin-top: 1.5rem; text-align: right;">
                <a href="{{ route('search') }}?q=Dell+Vostro" class="btn btn-primary">
                    <i class="ph ph-scales"></i> Cross-Reference on GeM
                </a>
            </div>
        </div>
        @else
        <div class="card" style="padding: 3rem; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; border: 1px dashed var(--glass-border);">
            <i class="ph ph-file-search" style="font-size: 4rem; color: var(--text-muted); opacity: 0.3; margin-bottom: 1.5rem;"></i>
            <h3 class="font-display" style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Awaiting Input</h3>
            <p style="color: var(--text-muted); max-width: 300px;">Upload a vendor quotation to see AI-extracted line items and pricing data here.</p>
        </div>
        @endif
    </div>
</div>
@endsection

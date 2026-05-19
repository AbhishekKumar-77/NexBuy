@extends('layouts.app')
@section('title', 'TCO Analysis — NexBuy')

@section('content')
<div class="fade-up">
    <div class="mb-8" style="text-align: center; max-width: 800px; margin: 0 auto 3rem;">
        <h1 class="font-display" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.75rem;">
            <i class="ph-fill ph-calculator text-gradient"></i> Total Cost of Ownership Engine
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6;">
            Calculate the true lifecycle cost of an asset. Base prices on GeM vs Commercial markets can be deceptive without accounting for logistics, GST variations, and Annual Maintenance Contracts.
        </p>
    </div>

    <div class="grid grid-2" style="grid-template-columns: 1fr 1.5fr; gap: 2rem;">
        
        <!-- Parameter Input -->
        <div class="card" style="padding: 2rem;">
            <h2 class="font-display" style="font-size: 1.2rem; font-weight: 600; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">Analysis Parameters</h2>
            
            <form id="tcoForm">
                <div class="form-group">
                    <label class="form-label">Procurement Asset</label>
                    <select class="form-control" id="product_id" style="width: 100%;">
                        <option value="">-- Select indexed asset --</option>
                        @foreach(\App\Models\Product::orderBy('name')->get() as $p)
                        <option value="{{ $p->id }}" data-gem="{{ $p->gem_price }}" data-amazon="{{ $p->amazon_price ?: $p->flipkart_price }}" @selected(request('product_id') == $p->id)>
                            {{ Str::limit($p->name, 60) }} (GeM: ₹{{ number_format($p->gem_price ?: 0, 0) }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Asset Lifespan (Years)</label>
                    <input type="number" class="form-control" id="lifespan" value="3" min="1" max="10">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" value="1" min="1">
                </div>

                <div style="background: rgba(255,255,255,0.02); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--glass-border); margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600;">GeM Baseline Cost (₹)</span>
                        <input type="number" class="form-control" id="base_gem" value="0" style="width: 120px; text-align: right; padding: 0.4rem 0.75rem;">
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600;">Commercial Baseline Cost (₹)</span>
                        <input type="number" class="form-control" id="base_comm" value="0" style="width: 120px; text-align: right; padding: 0.4rem 0.75rem;">
                    </div>
                </div>

                <button type="button" onclick="calculateTCO()" class="btn btn-primary" style="width: 100%;">
                    Execute Analysis <i class="ph ph-lightning"></i>
                </button>
            </form>
        </div>

        <!-- Telemetry Output -->
        <div class="card" style="padding: 2rem; background: linear-gradient(135deg, rgba(255,255,255,0.02), transparent);">
            <div class="flex justify-between items-center mb-6" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
                <h2 class="font-display" style="font-size: 1.2rem; font-weight: 600;">Simulation Matrix</h2>
                <span class="badge" style="background: rgba(124,58,237,0.1); color: #A78BFA; border: 1px solid rgba(124,58,237,0.3);">
                    <i class="ph-fill ph-arrows-clockwise"></i> Awaiting Execution
                </span>
            </div>

            <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
                <div style="flex: 1; padding: 1.5rem; background: rgba(245,158,11,0.05); border: 1px solid rgba(245,158,11,0.2); border-radius: var(--radius-sm); text-align: center;">
                    <div style="font-size: 0.8rem; color: var(--gem); font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem;">GeM Projection</div>
                    <div class="font-display" style="font-size: 2.2rem; font-weight: 800; color: white;" id="gem_total">₹0</div>
                </div>
                <div style="flex: 1; padding: 1.5rem; background: rgba(16,185,129,0.05); border: 1px solid rgba(16,185,129,0.2); border-radius: var(--radius-sm); text-align: center;">
                    <div style="font-size: 0.8rem; color: var(--accent); font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem;">Commercial Projection</div>
                    <div class="font-display" style="font-size: 2.2rem; font-weight: 800; color: white;" id="comm_total">₹0</div>
                </div>
            </div>

            <div style="background: rgba(0,0,0,0.3); border-radius: var(--radius-sm); border: 1px solid var(--glass-border); overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
                            <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Cost Vector</th>
                            <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; text-align: right;">GeM Network</th>
                            <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; text-align: right;">Commercial</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px dashed var(--glass-border);">
                            <td style="padding: 1rem; font-weight: 500;">Base Acquisition <span style="font-size: 0.75rem; color: var(--text-muted);">(incl. tax)</span></td>
                            <td style="padding: 1rem; text-align: right; font-weight: 600;" id="t_gem_base">₹0</td>
                            <td style="padding: 1rem; text-align: right; font-weight: 600;" id="t_comm_base">₹0</td>
                        </tr>
                        <tr style="border-bottom: 1px dashed var(--glass-border);">
                            <td style="padding: 1rem; font-weight: 500;">Logistics & Handling</td>
                            <td style="padding: 1rem; text-align: right; font-weight: 600; color: var(--success);" id="t_gem_log">₹0</td>
                            <td style="padding: 1rem; text-align: right; font-weight: 600;" id="t_comm_log">₹0</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--glass-border);">
                            <td style="padding: 1rem; font-weight: 500;">Lifecycle AMC <span style="font-size: 0.75rem; color: var(--text-muted);">(Est.)</span></td>
                            <td style="padding: 1rem; text-align: right; font-weight: 600;" id="t_gem_amc">₹0</td>
                            <td style="padding: 1rem; text-align: right; font-weight: 600;" id="t_comm_amc">₹0</td>
                        </tr>
                        <tr style="background: rgba(124,58,237,0.1);">
                            <td style="padding: 1rem; font-weight: 700; color: #C4B5FD;">Total TCO</td>
                            <td style="padding: 1rem; text-align: right; font-weight: 800; color: white;" id="t_gem_final">₹0</td>
                            <td style="padding: 1rem; text-align: right; font-weight: 800; color: white;" id="t_comm_final">₹0</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="conclusionBox" style="margin-top: 1.5rem; display: none; padding: 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--glass-border); text-align: center; background: rgba(255,255,255,0.02);">
                <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Intelligence Directive</div>
                <div class="font-display" id="conclusionText" style="font-size: 1.2rem; font-weight: 700; color: white;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('product_id').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        document.getElementById('base_gem').value = option.dataset.gem || 0;
        document.getElementById('base_comm').value = option.dataset.amazon || 0;
        if(this.value) calculateTCO();
    });
    
    // Auto trigger if prefilled
    if(document.getElementById('product_id').value) {
        document.getElementById('product_id').dispatchEvent(new Event('change'));
    }

    function f(val) {
        return new Intl.NumberFormat('en-IN').format(Math.round(val));
    }

    function calculateTCO() {
        const baseGem = parseFloat(document.getElementById('base_gem').value) || 0;
        const baseComm = parseFloat(document.getElementById('base_comm').value) || 0;
        const years = parseInt(document.getElementById('lifespan').value) || 3;
        const qty = parseInt(document.getElementById('quantity').value) || 1;

        if(!baseGem && !baseComm) return;

        // Base costs
        const totalBaseGem = baseGem * qty;
        const totalBaseComm = baseComm * qty;

        // Logistics (GeM generally includes it, commercial might charge ~2%)
        const logGem = 0;
        const logComm = totalBaseComm * 0.02;

        // AMC (GeM might have higher built in warranty, estimate 10% per year after 1 yr)
        const amcYears = Math.max(0, years - 1);
        const amcGem = totalBaseGem * 0.08 * amcYears; // 8% 
        const amcComm = totalBaseComm * 0.10 * amcYears; // 10%

        const finalGem = totalBaseGem + logGem + amcGem;
        const finalComm = totalBaseComm + logComm + amcComm;

        // Update DOM
        document.getElementById('t_gem_base').innerText = '₹' + f(totalBaseGem);
        document.getElementById('t_comm_base').innerText = '₹' + f(totalBaseComm);
        
        document.getElementById('t_gem_log').innerText = '₹' + f(logGem);
        document.getElementById('t_comm_log').innerText = '₹' + f(logComm);
        
        document.getElementById('t_gem_amc').innerText = '₹' + f(amcGem);
        document.getElementById('t_comm_amc').innerText = '₹' + f(amcComm);
        
        document.getElementById('gem_total').innerText = '₹' + f(finalGem);
        document.getElementById('comm_total').innerText = '₹' + f(finalComm);
        
        document.getElementById('t_gem_final').innerText = '₹' + f(finalGem);
        document.getElementById('t_comm_final').innerText = '₹' + f(finalComm);

        // Conclusion
        const conclusionBox = document.getElementById('conclusionBox');
        const conclusionText = document.getElementById('conclusionText');
        conclusionBox.style.display = 'block';

        if(finalGem <= finalComm) {
            conclusionBox.style.background = 'rgba(16,185,129,0.1)';
            conclusionBox.style.borderColor = 'rgba(16,185,129,0.3)';
            conclusionText.innerHTML = `<span style="color: var(--accent);">GeM Network Recommended.</span> Net savings of ₹${f(finalComm - finalGem)} over ${years} years.`;
        } else {
            conclusionBox.style.background = 'rgba(239,68,68,0.1)';
            conclusionBox.style.borderColor = 'rgba(239,68,68,0.3)';
            conclusionText.innerHTML = `<span style="color: var(--danger);">Commercial Procurement Advised.</span> GeM carries a premium of ₹${f(finalGem - finalComm)}. Requires justification.`;
        }
        
        // Update badge
        document.querySelector('.badge i').className = 'ph-fill ph-check-circle';
        document.querySelector('.badge').innerHTML = '<i class="ph-fill ph-check-circle"></i> Simulation Complete';
        document.querySelector('.badge').style.background = 'rgba(16,185,129,0.1)';
        document.querySelector('.badge').style.color = 'var(--accent)';
        document.querySelector('.badge').style.borderColor = 'rgba(16,185,129,0.3)';
    }
</script>
@endsection

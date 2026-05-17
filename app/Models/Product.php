<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'brand', 'category', 'subcategory', 'description', 'image_url',
        'gem_price', 'gem_product_id', 'gem_seller', 'gem_bis_certified',
        'gem_make_in_india', 'gem_msme_seller', 'gem_delivery_days',
        'gem_warranty_months', 'gem_seller_rating', 'gem_stock',
        'amazon_price', 'amazon_product_id', 'amazon_seller', 'amazon_rating',
        'amazon_reviews', 'amazon_delivery_days', 'amazon_warranty_months',
        'amazon_bis_certified', 'amazon_shipping',
        'flipkart_price', 'flipkart_product_id', 'flipkart_seller', 'flipkart_rating',
        'flipkart_reviews', 'flipkart_delivery_days', 'flipkart_warranty_months',
        'flipkart_bis_certified', 'flipkart_shipping',
        'indiamart_price', 'indiamart_seller', 'indiamart_moq', 'indiamart_delivery_days',
        'specifications', 'unit', 'gst_percent', 'gem_premium_score',
    ];

    protected $casts = [
        'specifications'       => 'array',
        'gem_bis_certified'    => 'boolean',
        'gem_make_in_india'    => 'boolean',
        'gem_msme_seller'      => 'boolean',
        'amazon_bis_certified' => 'boolean',
        'flipkart_bis_certified' => 'boolean',
    ];

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class);
    }

    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    /* ──────────── Helpers ──────────── */

    /** Lowest price across all platforms */
    public function getLowestPriceAttribute(): ?float
    {
        $prices = array_filter([
            $this->gem_price,
            $this->amazon_price,
            $this->flipkart_price,
            $this->indiamart_price,
        ]);
        return $prices ? min($prices) : null;
    }

    /** Platform name for lowest price */
    public function getLowestPlatformAttribute(): ?string
    {
        $map = [
            'GeM'       => $this->gem_price,
            'Amazon'    => $this->amazon_price,
            'Flipkart'  => $this->flipkart_price,
            'IndiaMART' => $this->indiamart_price,
        ];
        $filtered = array_filter($map);
        if (empty($filtered)) return null;
        return array_search(min($filtered), $filtered);
    }

    /** Price savings vs highest platform */
    public function getSavingsAttribute(): float
    {
        $prices = array_filter([
            $this->gem_price, $this->amazon_price,
            $this->flipkart_price, $this->indiamart_price,
        ]);
        if (count($prices) < 2) return 0;
        return max($prices) - min($prices);
    }

    /** Gem premium score label */
    public function getGemScoreLabelAttribute(): string
    {
        $score = (int) $this->gem_premium_score;
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 40) return 'Fair';
        return 'Poor';
    }

    /** Compute TCO for a given platform */
    public function computeTco(string $platform, int $quantity = 1, int $years = 3): array
    {
        $base     = $this->{$platform . '_price'} ?? 0;
        $shipping = $this->{$platform . '_shipping'} ?? 0;
        $warranty = $this->{$platform . '_warranty_months'} ?? 0;
        $gst      = $this->gst_percent ?? 18;

        $baseTotal     = $base * $quantity;
        $shippingTotal = $shipping * $quantity;
        $gstAmount     = $baseTotal * ($gst / 100);
        $amcPerYear    = ($warranty < ($years * 12)) ? ($base * 0.08 * $quantity) : 0;
        $amcTotal      = $amcPerYear * max(0, $years - ($warranty / 12));
        $total         = $baseTotal + $shippingTotal + $gstAmount + $amcTotal;

        return [
            'base_total'  => round($baseTotal, 2),
            'gst'         => round($gstAmount, 2),
            'shipping'    => round($shippingTotal, 2),
            'amc'         => round($amcTotal, 2),
            'total'       => round($total, 2),
        ];
    }

    /** Price drop prediction based on history or statistical trends */
    public function getPricePredictionAttribute(): array
    {
        // Mocking the AI/Statistical prediction logic.
        // In reality, this would analyze $this->priceHistories over the last 90 days
        // and look for patterns (e.g., drops at the end of the month, or Keepa API data).
        
        $trends = [
            [
                'action' => 'wait',
                'message' => 'Wait 5 days to buy. Historical data shows prices drop by ~10% near the end of the month.',
                'color' => 'var(--warning)',
                'icon' => '⏳'
            ],
            [
                'action' => 'buy',
                'message' => 'Buy Now! Price is at a 30-day low. Unlikely to drop further soon.',
                'color' => 'var(--success)',
                'icon' => '🔥'
            ],
            [
                'action' => 'caution',
                'message' => 'Price is artificially inflated by 15%. This is a fake sale. Wait for the real drop.',
                'color' => 'var(--danger)',
                'icon' => '🚨'
            ]
        ];

        // Pick a stable pseudo-random trend based on product ID so it doesn't change on every refresh
        return $trends[$this->id % count($trends)];
    }

    /** Vendor Trust & Compliance Score */
    public function getVendorTrustScoreAttribute(): array
    {
        // Mock dynamic vendor score based on seller properties
        $score = 100;
        $flags = [];

        if (!$this->gem_msme_seller) {
            $score -= 15;
            $flags[] = 'Not registered as MSME';
        } else {
            $flags[] = 'Verified MSME Vendor (+15)';
        }

        if (!$this->gem_make_in_india) {
            $score -= 20;
            $flags[] = 'Not Make in India certified';
        } else {
            $flags[] = 'Verified Make in India (+20)';
        }

        if ($this->gem_delivery_days > 7) {
            $score -= 10;
            $flags[] = 'Slow delivery track record';
        }

        // Simulate random backend check (e.g. GST/Blacklist API)
        $isGstValid = ($this->id % 3) !== 0; // 2/3 chance of valid GST
        if (!$isGstValid) {
            $score -= 30;
            $flags[] = '⚠️ GST INACTIVE or Invalid';
        }

        return [
            'score' => max(0, $score),
            'label' => $score >= 80 ? 'Highly Reliable' : ($score >= 60 ? 'Moderate Risk' : 'High Risk'),
            'color' => $score >= 80 ? 'var(--success)' : ($score >= 60 ? 'var(--warning)' : 'var(--danger)'),
            'details' => $flags
        ];
    }

    /** Advanced TCO with Energy & Depreciation */
    public function computeAdvancedTco(string $platform, int $quantity = 1, int $years = 3, int $wattage = 0, int $hoursPerDay = 0): array
    {
        $baseResult = $this->computeTco($platform, $quantity, $years);
        
        // 1. Energy Costing
        // kWh = (wattage * hours * 365 * years) / 1000
        // Assume avg cost of electricity is ₹8 / kWh
        $kwh = ($wattage * $hoursPerDay * 365 * $years) / 1000;
        $energyCost = $kwh * 8 * $quantity;
        
        // 2. Depreciation & Salvage Value
        // IT hardware generally depreciates to 10% after 3 years.
        $depreciationRate = 0.33; // 33% per year straight line
        $salvageValue = $baseResult['base_total'] * max(0.1, (1 - ($depreciationRate * $years)));

        $baseResult['energy_cost'] = round($energyCost, 2);
        $baseResult['salvage_value'] = round($salvageValue, 2);
        
        // Advanced Total = Base Total + Energy Cost - Salvage Value
        $baseResult['advanced_total'] = round($baseResult['total'] + $energyCost - $salvageValue, 2);

        return $baseResult;
    }
}

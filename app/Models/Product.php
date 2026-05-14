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
}

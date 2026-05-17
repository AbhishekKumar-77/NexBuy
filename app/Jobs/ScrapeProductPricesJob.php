<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\PriceHistory;

class ScrapeProductPricesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $product;
    public $platform;

    /**
     * Create a new job instance.
     */
    public function __construct(Product $product, string $platform)
    {
        $this->product = $product;
        $this->platform = $platform; // e.g., 'amazon', 'flipkart'
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting background scrape job for Product ID {$this->product->id} on {$this->platform}");

        // In a real application, you would make an API call to a scraping proxy (like BrightData)
        // or a dedicated Keepa/CamelCamelCamel API here to avoid IP blocks.
        
        // Mocking Keepa API / Scraper response:
        sleep(rand(1, 3)); // Simulate network latency

        // Simulate fetching a slightly different price to simulate live market fluctuations
        $currentPriceField = $this->platform . '_price';
        $basePrice = $this->product->$currentPriceField ?? ($this->product->gem_price * rand(8, 12) / 10);
        
        // Fluctuate price by +/- 5%
        $fluctuation = $basePrice * (rand(-5, 5) / 100);
        $newPrice = round($basePrice + $fluctuation, 2);

        // Update the Product record
        $this->product->update([
            $currentPriceField => $newPrice
        ]);

        // Record it in PriceHistory for the prediction engine
        PriceHistory::create([
            'product_id' => $this->product->id,
            'platform' => $this->platform,
            'price' => $newPrice,
            'recorded_date' => now()->startOfDay()
        ]);

        Log::info("Successfully updated {$this->platform} price to ₹{$newPrice} for Product ID {$this->product->id}");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Jobs\ScrapeProductPricesJob;

class ScrapePrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:prices {--platform=all : The platform to scrape (amazon, flipkart, or all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch asynchronous background jobs to scrape live prices for all products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $platformArg = $this->option('platform');
        $platforms = $platformArg === 'all' ? ['amazon', 'flipkart'] : [$platformArg];

        $this->info("🚀 Initializing Asynchronous Scraping Engine...");
        
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->warn("No products found in the database to scrape.");
            return Command::SUCCESS;
        }

        $this->info("Found {$products->count()} products. Dispatching jobs to Redis/Horizon Queue...");

        $bar = $this->output->createProgressBar($products->count() * count($platforms));
        $bar->start();

        foreach ($products as $product) {
            foreach ($platforms as $platform) {
                // Dispatch job to the queue
                ScrapeProductPricesJob::dispatch($product, $platform)
                    ->onQueue('scraping'); // Requires `php artisan queue:work --queue=scraping`
                
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ All scraping tasks have been successfully queued.");
        $this->info("Run 'php artisan queue:work --queue=scraping' or check Laravel Horizon to process them.");

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ScrapePrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:amazon {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape a product price from Amazon to demonstrate live data fetching';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = $this->argument('url');
        
        $this->info("🔍 Initializing scraper...");
        $this->info("🌐 Target URL: " . $url);

        // We must spoof our headers so Amazon doesn't instantly block us as a bot
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Connection' => 'keep-alive',
            'Upgrade-Insecure-Requests' => '1',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'none',
            'Sec-Fetch-User' => '?1',
            'Cache-Control' => 'max-age=0',
        ];

        try {
            $this->info("⏳ Sending HTTP GET request to Amazon...");
            $response = Http::withHeaders($headers)->get($url);

            if ($response->failed()) {
                $this->error("❌ HTTP Request failed. Status code: " . $response->status());
                return Command::FAILURE;
            }

            $html = $response->body();
            
            // Check if Amazon served a CAPTCHA instead of the product page
            if (str_contains($html, 'api/services/captcha')) {
                $this->error("⚠️ ANTI-BOT TRIGGERED: Amazon served a CAPTCHA page.");
                $this->warn("This is why production apps use services like BrightData or ScrapingBee which rotate thousands of residential IP addresses.");
                return Command::FAILURE;
            }

            $this->info("✅ Page downloaded successfully. Parsing HTML...");
            
            $crawler = new Crawler($html);

            // Scrape Title
            $title = 'Unknown Product';
            if ($crawler->filter('#productTitle')->count() > 0) {
                $title = trim($crawler->filter('#productTitle')->text());
            }

            // Scrape Price
            // Amazon has multiple price classes depending on the layout. We check a few common ones.
            $price = null;
            $priceSelectors = [
                '.a-price.a-text-price.a-size-medium.apexPriceToPay .a-offscreen',
                '.a-price.aok-align-center .a-offscreen',
                '#priceblock_ourprice',
                '#priceblock_dealprice',
                '.a-price .a-offscreen'
            ];

            foreach ($priceSelectors as $selector) {
                if ($crawler->filter($selector)->count() > 0) {
                    $priceText = trim($crawler->filter($selector)->first()->text());
                    // Remove currency symbol and commas
                    $price = preg_replace('/[^0-9.]/', '', $priceText);
                    if (!empty($price)) {
                        break;
                    }
                }
            }

            if ($price) {
                $this->info("--------------------------------------------------");
                $this->info("📦 Product: " . $title);
                $this->info("💰 Live Price: ₹" . number_format((float)$price, 2));
                $this->info("--------------------------------------------------");
                $this->info("Database would normally be updated here.");
                return Command::SUCCESS;
            } else {
                $this->error("❌ Could not find the price on the page. Amazon might have changed their HTML layout, or the item is out of stock.");
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("❌ An exception occurred: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

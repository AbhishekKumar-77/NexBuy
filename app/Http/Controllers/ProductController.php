<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PriceHistory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** Landing / home page */
    public function index()
    {
        $categories   = Product::select('category')->distinct()->pluck('category');
        $featuredProducts = Product::latest()->take(8)->get();
        $stats = [
            'total_products'   => Product::count(),
            'avg_savings'      => round(Product::all()->avg('savings'), 2),
            'categories'       => $categories->count(),
            'gem_cheaper_count' => Product::whereNotNull('gem_price')
                ->whereNotNull('amazon_price')
                ->whereColumn('gem_price', '<', 'amazon_price')
                ->count(),
        ];
        return view('home', compact('categories', 'featuredProducts', 'stats'));
    }

    /** Analytics Dashboard */
    public function dashboard()
    {
        $products = Product::all();
        $totalProducts = $products->count();
        $totalSavings = round($products->sum('savings'), 0);
        $gemCheaperCount = Product::whereNotNull('gem_price')
            ->whereNotNull('amazon_price')
            ->whereColumn('gem_price', '<', 'amazon_price')
            ->count();

        // Anomaly count
        $anomalyCount = $products->filter(function ($p) {
            $marketPrices = array_filter([$p->amazon_price, $p->flipkart_price]);
            if (!$p->gem_price || count($marketPrices) < 1) return false;
            $avgMarket = array_sum($marketPrices) / count($marketPrices);
            return $avgMarket > 0 && ($p->gem_price / $avgMarket) > 1.3;
        })->count();

        // Platform distribution: which platform wins most
        $platformDistribution = ['GeM' => 0, 'Amazon' => 0, 'Flipkart' => 0, 'IndiaMART' => 0];
        foreach ($products as $p) {
            $lp = $p->lowest_platform;
            if ($lp && isset($platformDistribution[$lp])) {
                $platformDistribution[$lp]++;
            }
        }

        // Category savings
        $categorySavings = $products->groupBy('category')->map(function ($items, $cat) {
            return [
                'category' => $cat,
                'avg_savings' => round($items->avg('savings'), 0),
            ];
        })->values();

        // Category health index
        $categoryHealth = $products->groupBy('category')->map(function ($items, $cat) {
            $avgGem = $items->where('gem_price', '>', 0)->avg('gem_price') ?: 0;
            $avgMarket = $items->map(function ($p) {
                $prices = array_filter([$p->amazon_price, $p->flipkart_price]);
                return count($prices) ? array_sum($prices) / count($prices) : 0;
            })->filter()->avg() ?: 0;
            $delta = $avgMarket > 0 ? round((($avgGem / $avgMarket) - 1) * 100, 1) : 0;
            return [
                'category' => $cat,
                'avg_gem' => $avgGem,
                'avg_market' => $avgMarket,
                'delta' => $delta,
            ];
        })->values();

        return view('dashboard', compact(
            'totalProducts', 'totalSavings', 'gemCheaperCount', 'anomalyCount',
            'platformDistribution', 'categorySavings', 'categoryHealth'
        ));
    }

    /** Search products */
    public function search(Request $request)
    {
        $query    = $request->input('q', '');
        $category = $request->input('category', '');
        $sort     = $request->input('sort', 'name');
        $platform = $request->input('platform', '');

        $buildQuery = function() use ($query, $category, $platform, $sort) {
            return Product::query()
                ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%")
                    ->orWhere('brand', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('category', 'like', "%{$query}%"))
                ->when($category, fn($q) => $q->where('category', $category))
                ->when($platform === 'gem', fn($q) => $q->whereNotNull('gem_price'))
                ->when($platform === 'amazon', fn($q) => $q->whereNotNull('amazon_price'))
                ->when($platform === 'flipkart', fn($q) => $q->whereNotNull('flipkart_price'))
                ->orderBy($sort === 'gem_price' ? 'gem_price' : ($sort === 'amazon_price' ? 'amazon_price' : 'name'));
        };

        $productsCount = $buildQuery()->count();

        // If no products found in local DB, fetch from external APIs (Simulated)
        if ($query && $productsCount === 0) {
            $this->simulateExternalFetch($query);
        }

        // Fetch again to include the newly scraped products
        $products = $buildQuery()->paginate(12)->withQueryString();

        $categories = Product::select('category')->distinct()->pluck('category');
        return view('search', compact('products', 'query', 'category', 'sort', 'platform', 'categories'));
    }

    /** Product detail + compare page */
    public function show(Product $product)
    {
        // Price history per platform for the last 90 days
        $history = PriceHistory::where('product_id', $product->id)
            ->where('recorded_date', '>=', now()->subDays(90))
            ->orderBy('recorded_date')
            ->get()
            ->groupBy('platform');

        $labels = [];
        $gemData      = [];
        $amazonData   = [];
        $flipkartData = [];

        // Build labels from all unique dates
        $allDates = PriceHistory::where('product_id', $product->id)
            ->where('recorded_date', '>=', now()->subDays(90))
            ->distinct('recorded_date')
            ->pluck('recorded_date')
            ->map(fn($d) => $d->format('d M'))
            ->unique()
            ->values();

        $labels = $allDates;

        foreach (['gem', 'amazon', 'flipkart'] as $platform) {
            $map = collect($history->get($platform, []))->keyBy(fn($h) => $h->recorded_date->format('d M'));
            $data = [];
            foreach ($allDates as $lbl) {
                $data[] = isset($map[$lbl]) ? (float) $map[$lbl]->price : null;
            }
            if ($platform === 'gem')      $gemData      = $data;
            if ($platform === 'amazon')   $amazonData   = $data;
            if ($platform === 'flipkart') $flipkartData = $data;
        }

        // Similar products
        $similar = Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('product', compact(
            'product', 'labels', 'gemData', 'amazonData', 'flipkartData', 'similar'
        ));
    }

    /** TCO Calculator page */
    public function tco(Request $request)
    {
        $product  = null;
        $results  = [];

        if ($request->filled('product_id')) {
            $product  = Product::findOrFail($request->product_id);
            $quantity = (int) $request->input('quantity', 1);
            $years    = (int) $request->input('years', 3);

            foreach (['gem', 'amazon', 'flipkart', 'indiamart'] as $platform) {
                if ($product->{$platform . '_price'}) {
                    // Assuming default wattage of 150W and 8 hours usage per day for the advanced calc
                    $results[$platform] = $product->computeAdvancedTco($platform, $quantity, $years, 150, 8);
                }
            }
        }

        $products = Product::select('id', 'name', 'brand')->get();
        return view('tco', compact('product', 'results', 'products'));
    }

    /** Watchlist page */
    public function watchlist(Request $request)
    {
        $sessionId = $request->session()->getId();
        $items     = \App\Models\Watchlist::where('session_id', $sessionId)
            ->with('product')
            ->latest()
            ->get();
        return view('watchlist', compact('items'));
    }

    /** Add to watchlist */
    public function addWatchlist(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        \App\Models\Watchlist::firstOrCreate([
            'session_id' => $request->session()->getId(),
            'product_id' => $request->product_id,
        ], ['alert_price' => $request->alert_price]);

        return back()->with('success', 'Added to watchlist!');
    }

    /** Remove from watchlist */
    public function removeWatchlist(Request $request)
    {
        \App\Models\Watchlist::where('session_id', $request->session()->getId())
            ->where('product_id', $request->product_id)
            ->delete();
        return back()->with('success', 'Removed from watchlist.');
    }

    /** Comparative Statement PDF / Print */
    public function csReport(Request $request)
    {
        $productIds = array_filter(explode(',', $request->input('ids', '')));
        $products   = Product::whereIn('id', $productIds)->get();
        $quantity   = (int) $request->input('quantity', 1);
        $dept       = $request->input('dept', 'Procurement Department');
        $officer    = $request->input('officer', '');

        return view('cs_report', compact('products', 'quantity', 'dept', 'officer'));
    }

    /** Export CS Report to CSV (Excel Compatible) */
    public function exportCS(Request $request)
    {
        $productIds = array_filter(explode(',', $request->input('ids', '')));
        $products   = Product::whereIn('id', $productIds)->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=nexbuy_cs_report_" . date('Y-m-d') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Product Name', 'Brand', 'GeM Price', 'Amazon Price', 'Flipkart Price', 'Lowest Price', 'Savings'];

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($products as $p) {
                fputcsv($file, [
                    $p->name,
                    $p->brand,
                    $p->gem_price,
                    $p->amazon_price,
                    $p->flipkart_price,
                    $p->lowest_price,
                    $p->savings
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /** Anomaly / fraud flags */
    public function anomalies()
    {
        // Flag products where GeM is >30% higher than market average
        $products = Product::all()->filter(function ($p) {
            $marketPrices = array_filter([$p->amazon_price, $p->flipkart_price]);
            if (!$p->gem_price || count($marketPrices) < 1) return false;
            $avgMarket = array_sum($marketPrices) / count($marketPrices);
            return $avgMarket > 0 && ($p->gem_price / $avgMarket) > 1.3;
        });

        // Flag products suspiciously cheap on commercial (potential counterfeit)
        $counterfeitRisk = Product::all()->filter(function ($p) {
            $prices = array_filter([$p->gem_price, $p->flipkart_price]);
            if (!$p->amazon_price || count($prices) < 1) return false;
            $avgOther = array_sum($prices) / count($prices);
            return $avgOther > 0 && ($p->amazon_price / $avgOther) < 0.6;
        });

        return view('anomalies', compact('products', 'counterfeitRisk'));
    }

    /** Fetch realistic product data when a search term has no local results */
    private function simulateExternalFetch(string $query)
    {
        // Product intelligence database — maps keywords to realistic govt procurement items
        $catalog = [
            'laptop' => [
                ['name'=>'Dell Latitude 5540 Business Laptop (i5, 16GB, 512GB)','brand'=>'Dell','cat'=>'IT Equipment','img'=>'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400','base'=>58000,'specs'=>['Processor'=>'Intel i5-1345U','RAM'=>'16GB DDR5','Storage'=>'512GB NVMe SSD','Display'=>'15.6" FHD IPS','OS'=>'Windows 11 Pro']],
                ['name'=>'HP ProBook 450 G10 Notebook (i5, 8GB, 256GB)','brand'=>'HP','cat'=>'IT Equipment','img'=>'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=400','base'=>45000,'specs'=>['Processor'=>'Intel i5-1335U','RAM'=>'8GB DDR4','Storage'=>'256GB SSD','Display'=>'15.6" FHD','OS'=>'Windows 11 Pro']],
                ['name'=>'Lenovo ThinkPad E14 Gen 5 (i7, 16GB, 512GB)','brand'=>'Lenovo','cat'=>'IT Equipment','img'=>'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400','base'=>72000,'specs'=>['Processor'=>'Intel i7-1355U','RAM'=>'16GB DDR5','Storage'=>'512GB SSD','Display'=>'14" FHD IPS','OS'=>'Windows 11 Pro']],
            ],
            'printer' => [
                ['name'=>'HP LaserJet Pro M404dn Mono Printer','brand'=>'HP','cat'=>'IT Equipment','img'=>'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=400','base'=>22000,'specs'=>['Type'=>'Mono Laser','Speed'=>'38 ppm','Connectivity'=>'USB + Ethernet','Duplex'=>'Auto','Duty Cycle'=>'80,000 pages/month']],
                ['name'=>'Canon imageCLASS MF269dw Multifunction Printer','brand'=>'Canon','cat'=>'IT Equipment','img'=>'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=400','base'=>28000,'specs'=>['Type'=>'Mono Laser MFP','Speed'=>'28 ppm','Functions'=>'Print/Scan/Copy/Fax','Connectivity'=>'WiFi + USB + LAN']],
                ['name'=>'Epson EcoTank L3250 Ink Tank Printer','brand'=>'Epson','cat'=>'IT Equipment','img'=>'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=400','base'=>12500,'specs'=>['Type'=>'Ink Tank Color','Speed'=>'10 ppm','Connectivity'=>'WiFi + USB','Ink System'=>'EcoTank Refillable']],
            ],
            'chair' => [
                ['name'=>'Godrej Interio Motion High-Back Office Chair','brand'=>'Godrej','cat'=>'Office Furniture','img'=>'https://images.unsplash.com/photo-1541558869434-2840d308329a?w=400','base'=>12500,'specs'=>['Material'=>'Mesh + Metal','Armrest'=>'Adjustable','Wheels'=>'Nylon Castor','Weight Capacity'=>'120 kg','BIS'=>'IS 4535']],
                ['name'=>'Featherlite Amigo HB Ergonomic Chair','brand'=>'Featherlite','cat'=>'Office Furniture','img'=>'https://images.unsplash.com/photo-1541558869434-2840d308329a?w=400','base'=>9800,'specs'=>['Material'=>'Fabric + Metal','Lumbar Support'=>'Yes','Tilt Mechanism'=>'Synchro','Warranty'=>'5 Years']],
                ['name'=>'Nilkamal Executive Leatherette Office Chair','brand'=>'Nilkamal','cat'=>'Office Furniture','img'=>'https://images.unsplash.com/photo-1541558869434-2840d308329a?w=400','base'=>7200,'specs'=>['Material'=>'Leatherette','Armrest'=>'Fixed','Wheels'=>'PU Castor','Weight Capacity'=>'100 kg']],
            ],
            'monitor' => [
                ['name'=>'Dell P2422H 24" FHD IPS Monitor','brand'=>'Dell','cat'=>'IT Equipment','img'=>'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400','base'=>14500,'specs'=>['Size'=>'24 inch','Panel'=>'IPS','Resolution'=>'1920x1080','Ports'=>'HDMI + DP + VGA','Stand'=>'Height Adjustable']],
                ['name'=>'LG 27UK850-W 27" 4K USB-C Monitor','brand'=>'LG','cat'=>'IT Equipment','img'=>'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400','base'=>32000,'specs'=>['Size'=>'27 inch','Panel'=>'IPS','Resolution'=>'3840x2160 4K','HDR'=>'HDR10','USB-C'=>'60W Power Delivery']],
                ['name'=>'Samsung LS24A33 24" FHD LED Monitor','brand'=>'Samsung','cat'=>'IT Equipment','img'=>'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400','base'=>9800,'specs'=>['Size'=>'24 inch','Panel'=>'VA','Resolution'=>'1920x1080','Ports'=>'HDMI + D-Sub','Response'=>'5ms']],
            ],
            'projector' => [
                ['name'=>'Epson EB-X51 XGA 3LCD Projector','brand'=>'Epson','cat'=>'IT Equipment','img'=>'https://images.unsplash.com/photo-1478720568477-152d9b164e26?w=400','base'=>42000,'specs'=>['Type'=>'3LCD','Brightness'=>'3800 Lumens','Resolution'=>'XGA (1024x768)','Connectivity'=>'HDMI + VGA','Lamp Life'=>'12000 hrs']],
                ['name'=>'BenQ MS560 SVGA DLP Projector','brand'=>'BenQ','cat'=>'IT Equipment','img'=>'https://images.unsplash.com/photo-1478720568477-152d9b164e26?w=400','base'=>32000,'specs'=>['Type'=>'DLP','Brightness'=>'4000 Lumens','Resolution'=>'SVGA','Connectivity'=>'HDMI x2','SmartEco'=>'15000 hrs']],
            ],
            'ac' => [
                ['name'=>'Voltas 1.5 Ton 3 Star Split AC','brand'=>'Voltas','cat'=>'Electrical & Power','img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400','base'=>35000,'specs'=>['Capacity'=>'1.5 Ton','Star Rating'=>'3 Star','Type'=>'Split Inverter','Refrigerant'=>'R32','Copper Condenser'=>'Yes']],
                ['name'=>'Blue Star IC318DATU 1.5T 3 Star Inverter AC','brand'=>'Blue Star','cat'=>'Electrical & Power','img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400','base'=>38000,'specs'=>['Capacity'=>'1.5 Ton','Star Rating'=>'3 Star','Type'=>'Inverter Split','Filter'=>'Silver Ion','BIS'=>'IS 1391']],
            ],
            'sanitizer' => [
                ['name'=>'Dettol Hand Sanitizer 500ml (Pack of 4)','brand'=>'Dettol','cat'=>'Cleaning & Hygiene','img'=>'https://images.unsplash.com/photo-1584515933487-779824d29309?w=400','base'=>680,'specs'=>['Volume'=>'500ml x 4','Type'=>'Gel','Alcohol'=>'70%','Kills'=>'99.9% germs']],
                ['name'=>'Lifebuoy Total 10 Hand Sanitizer 1L','brand'=>'Lifebuoy','cat'=>'Cleaning & Hygiene','img'=>'https://images.unsplash.com/photo-1584515933487-779824d29309?w=400','base'=>320,'specs'=>['Volume'=>'1 Litre','Type'=>'Liquid','Alcohol'=>'60%']],
            ],
            'paper' => [
                ['name'=>'JK Copier A4 75 GSM Paper (5 Reams)','brand'=>'JK Paper','cat'=>'Stationery','img'=>'https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=400','base'=>1550,'specs'=>['Size'=>'A4','GSM'=>'75','Sheets'=>'500 per ream','Pack'=>'5 Reams','Brightness'=>'94%']],
                ['name'=>'Century Pulp A4 70 GSM Copier Paper (10 Reams)','brand'=>'Century','cat'=>'Stationery','img'=>'https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=400','base'=>2800,'specs'=>['Size'=>'A4','GSM'=>'70','Sheets'=>'500 per ream','Pack'=>'10 Reams']],
            ],
        ];

        // Find the best matching category from keywords
        $lowerQuery = strtolower($query);
        $matched = null;

        foreach ($catalog as $keyword => $items) {
            if (stripos($lowerQuery, $keyword) !== false) {
                $matched = $items;
                break;
            }
        }

        // Broad keyword fallback mapping
        if (!$matched) {
            $keywordMap = [
                'computer' => 'laptop', 'notebook' => 'laptop', 'pc' => 'laptop', 'macbook' => 'laptop',
                'desk' => 'chair', 'table' => 'chair', 'furniture' => 'chair', 'seat' => 'chair',
                'toner' => 'printer', 'ink' => 'printer', 'copier' => 'printer', 'scanner' => 'printer',
                'screen' => 'monitor', 'display' => 'monitor', 'lcd' => 'monitor', 'led' => 'monitor',
                'phone' => 'laptop', 'mobile' => 'laptop', 'tablet' => 'laptop', 'ipad' => 'laptop',
                'clean' => 'sanitizer', 'soap' => 'sanitizer', 'mask' => 'sanitizer', 'wash' => 'sanitizer',
                'pen' => 'paper', 'notebook' => 'paper', 'stationery' => 'paper', 'register' => 'paper',
                'cooling' => 'ac', 'fan' => 'ac', 'air' => 'ac', 'cooler' => 'ac',
                'beam' => 'projector', 'presentation' => 'projector',
                'ups' => 'ac', 'inverter' => 'ac', 'battery' => 'ac',
                'iphone' => 'laptop', 'samsung' => 'laptop', 'dell' => 'laptop', 'hp' => 'laptop',
            ];

            foreach ($keywordMap as $kw => $catKey) {
                if (stripos($lowerQuery, $kw) !== false && isset($catalog[$catKey])) {
                    $matched = $catalog[$catKey];
                    break;
                }
            }
        }

        // Ultimate fallback — try DummyJSON API
        if (!$matched) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)
                    ->get("https://dummyjson.com/products/search?q=" . urlencode($query));

                if ($response->successful() && !empty($response->json('products'))) {
                    foreach (array_slice($response->json('products'), 0, 3) as $apiProduct) {
                        $basePriceInr = round($apiProduct['price'] * 83);
                        $gemVariation = rand(98, 108) / 100;
                        $flipVariation = rand(94, 102) / 100;

                        Product::create([
                            'name' => $apiProduct['title'],
                            'brand' => $apiProduct['brand'] ?? 'Generic',
                            'category' => ucfirst(str_replace(['-', '_'], ' ', $apiProduct['category'] ?? 'General')),
                            'description' => $apiProduct['description'] ?? '',
                            'image_url' => $apiProduct['thumbnail'],
                            'gem_price' => round($basePriceInr * $gemVariation),
                            'gem_seller' => 'GeM Verified Vendor',
                            'gem_bis_certified' => true,
                            'gem_make_in_india' => (bool)rand(0, 1),
                            'gem_msme_seller' => (bool)rand(0, 1),
                            'gem_delivery_days' => rand(4, 10),
                            'gem_premium_score' => rand(55, 90),
                            'amazon_price' => $basePriceInr,
                            'amazon_seller' => 'Amazon India',
                            'amazon_rating' => round($apiProduct['rating'] ?? 4.0, 1),
                            'amazon_delivery_days' => rand(1, 4),
                            'amazon_bis_certified' => false,
                            'flipkart_price' => round($basePriceInr * $flipVariation),
                            'flipkart_seller' => 'Flipkart Assured',
                            'flipkart_delivery_days' => rand(2, 5),
                            'indiamart_price' => round($basePriceInr * 0.88),
                            'indiamart_seller' => 'Wholesale Supplier',
                            'indiamart_moq' => rand(3, 10),
                            'indiamart_delivery_days' => rand(7, 14),
                            'specifications' => ['Rating' => ($apiProduct['rating'] ?? '4.0') . '/5', 'Warranty' => rand(1,2) . ' Year'],
                            'gst_percent' => 18,
                        ]);
                    }
                    return;
                }
            } catch (\Exception $e) { /* fallback below */ }

            // Final fallback — generate generic but realistic product
            $matched = [
                ['name' => ucwords($query) . ' (Standard Model)', 'brand' => 'Generic India', 'cat' => 'General Supplies', 'img' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400', 'base' => rand(500, 25000), 'specs' => ['Type' => ucwords($query), 'Origin' => 'India', 'Warranty' => '1 Year', 'Condition' => 'New']],
                ['name' => ucwords($query) . ' (Premium Model)', 'brand' => 'BrandX India', 'cat' => 'General Supplies', 'img' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400', 'base' => rand(1000, 35000), 'specs' => ['Type' => ucwords($query), 'Origin' => 'India', 'Warranty' => '2 Years', 'Grade' => 'Commercial']],
            ];
        }

        // Create products from matched catalog
        $gemSellers = ['TechSupplies India Pvt Ltd', 'National IT Solutions', 'BharatTech Ventures', 'Swadeshi Digital Pvt Ltd', 'Govt Authorized Reseller'];
        $amazonSellers = ['Amazon India Official', 'CloudTail India', 'Appario Retail', 'RetailNet India'];
        $flipkartSellers = ['Flipkart Assured', 'SuperComNet', 'TechWorld Retail', 'OmniTech India'];
        $indiamartSellers = ['Delhi IT Hub', 'Mumbai Wholesale Co.', 'Hyderabad Electronics', 'Pune Supply Chain'];

        foreach ($matched as $item) {
            $base = $item['base'];
            // Realistic price variations: GeM is 0-8% higher, Flipkart 2-6% lower, IndiaMART 10-15% lower for bulk
            $gemPrice = round($base * (rand(100, 108) / 100));
            $amazonPrice = round($base * (rand(97, 103) / 100));
            $flipkartPrice = round($base * (rand(94, 101) / 100));
            $indiamartPrice = round($base * (rand(85, 93) / 100));

            Product::create([
                'name' => $item['name'],
                'brand' => $item['brand'],
                'category' => $item['cat'],
                'description' => "Government procurement grade {$item['name']}. Available for comparison across GeM, Amazon, Flipkart and IndiaMART.",
                'image_url' => $item['img'],
                'gem_price' => $gemPrice,
                'gem_seller' => $gemSellers[array_rand($gemSellers)],
                'gem_bis_certified' => true,
                'gem_make_in_india' => (bool)rand(0, 1),
                'gem_msme_seller' => (bool)rand(0, 1),
                'gem_delivery_days' => rand(4, 10),
                'gem_warranty_months' => rand(12, 36),
                'gem_seller_rating' => number_format(rand(38, 48) / 10, 1),
                'gem_premium_score' => rand(55, 92),
                'amazon_price' => $amazonPrice,
                'amazon_seller' => $amazonSellers[array_rand($amazonSellers)],
                'amazon_rating' => round(rand(38, 47) / 10, 1),
                'amazon_reviews' => rand(200, 12000),
                'amazon_delivery_days' => rand(1, 4),
                'amazon_warranty_months' => rand(6, 24),
                'amazon_bis_certified' => (bool)rand(0, 1),
                'amazon_shipping' => 0,
                'flipkart_price' => $flipkartPrice,
                'flipkart_seller' => $flipkartSellers[array_rand($flipkartSellers)],
                'flipkart_rating' => round(rand(37, 46) / 10, 1),
                'flipkart_reviews' => rand(100, 8000),
                'flipkart_delivery_days' => rand(2, 5),
                'flipkart_warranty_months' => rand(6, 24),
                'flipkart_shipping' => 0,
                'indiamart_price' => $indiamartPrice,
                'indiamart_seller' => $indiamartSellers[array_rand($indiamartSellers)],
                'indiamart_moq' => rand(3, 15),
                'indiamart_delivery_days' => rand(7, 14),
                'specifications' => $item['specs'],
                'gst_percent' => 18,
            ]);
        }
    }
}

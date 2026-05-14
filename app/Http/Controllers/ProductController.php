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

    /** Search products */
    public function search(Request $request)
    {
        $query    = $request->input('q', '');
        $category = $request->input('category', '');
        $sort     = $request->input('sort', 'name');
        $platform = $request->input('platform', '');

        $products = Product::query()
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%")
                ->orWhere('brand', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%"))
            ->when($category, fn($q) => $q->where('category', $category))
            ->when($platform === 'gem', fn($q) => $q->whereNotNull('gem_price'))
            ->when($platform === 'amazon', fn($q) => $q->whereNotNull('amazon_price'))
            ->when($platform === 'flipkart', fn($q) => $q->whereNotNull('flipkart_price'))
            ->orderBy($sort === 'gem_price' ? 'gem_price' : ($sort === 'amazon_price' ? 'amazon_price' : 'name'))
            ->paginate(12)
            ->withQueryString();

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
                    $results[$platform] = $product->computeTco($platform, $quantity, $years);
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
}

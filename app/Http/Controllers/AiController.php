<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiProcurementService;
use App\Models\Product;

class AiController extends Controller
{
    protected $aiService;

    public function __construct(AiProcurementService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * AI Product Matcher View
     */
    public function matcher(Request $request)
    {
        $products = Product::whereNotNull('gem_price')->whereNotNull('amazon_price')->get();
        $selectedProduct = null;
        $analysisResult = null;

        if ($request->filled('product_id')) {
            $selectedProduct = Product::findOrFail($request->product_id);
            
            // Format data for AI
            $gemData = [
                'title' => $selectedProduct->name . ' (GeM)',
                'brand' => $selectedProduct->brand,
                'specs' => $selectedProduct->specifications
            ];
            
            $commercialData = [
                'title' => $selectedProduct->name . ' (Amazon/Flipkart)',
                'brand' => $selectedProduct->brand,
                'specs' => $selectedProduct->specifications // In reality, this would be scraped specs
            ];

            $analysisResult = $this->aiService->matchProducts($gemData, $commercialData);
        }

        return view('ai.matcher', compact('products', 'selectedProduct', 'analysisResult'));
    }

    /**
     * Automated RFP Generator View
     */
    public function rfp(Request $request)
    {
        $products = Product::all();
        $generatedRfp = null;
        $selectedProduct = null;

        if ($request->filled('product_id')) {
            $selectedProduct = Product::findOrFail($request->product_id);
            $department = $request->input('department', 'Ministry of Electronics and Information Technology');
            
            $specs = is_array($selectedProduct->specifications) ? $selectedProduct->specifications : [
                'Processor' => 'Intel Core i5',
                'RAM' => '8GB',
                'Storage' => '512GB SSD'
            ];

            $generatedRfp = $this->aiService->generateRfp($specs, $department);
        }

        return view('ai.rfp', compact('products', 'selectedProduct', 'generatedRfp'));
    }

    /**
     * OCR Quotation Comparison View
     */
    public function ocr(Request $request)
    {
        $extractedData = null;

        if ($request->isMethod('post') && $request->hasFile('quotation_file')) {
            // Process the uploaded file
            $file = $request->file('quotation_file');
            $extractedData = $this->aiService->extractQuotationData($file);
            session()->flash('success', 'Quotation successfully analyzed using AI OCR.');
        }

        return view('ai.ocr', compact('extractedData'));
    }
}

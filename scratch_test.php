<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

try {
    echo "Running original discountProducts query...\n";
    $discountProducts = Product::select('products.*')
                                ->with(['brand', 'category', 'skus', 'images' => function($q) {
                                    $q->where('is_primary', true);
                                }])
                                ->whereHas('skus', function($q) {
                                    $q->whereNotNull('discount_price')->where('stock', '>', 0);
                                })
                                ->addSelect(['max_discount_pct' => DB::table('product_skus')
                                    ->whereColumn('product_id', 'products.id')
                                    ->whereNotNull('discount_price')
                                    ->selectRaw('MAX(((base_price - discount_price) / base_price) * 100)')
                                ])
                                ->withAvg('reviews', 'rating')
                                ->withCount('reviews') 
                                ->orderBy('max_discount_pct', 'DESC') 
                                ->latest()
                                ->take(8) 
                                ->get();
    echo "Query completed successfully! Count: " . count($discountProducts) . "\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

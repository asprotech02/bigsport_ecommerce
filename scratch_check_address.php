<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
$order = Order::with('shippingDetail')->whereNotNull('status')->latest()->first();
if ($order) {
    echo "ID: " . $order->id . "\n";
} else {
    echo "No orders found.\n";
}

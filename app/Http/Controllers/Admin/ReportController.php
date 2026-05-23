<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        // Stats
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['paid', 'settlement'])
            ->sum('grand_total');
            
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count();
        $cancelledOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'cancelled')->count();

        // Grafik Penjualan Harian
        $salesData = DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->whereIn('payment_status', ['paid', 'settlement'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Best Sellers (Produk Terlaris)
        $bestSellers = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_skus', 'order_items.product_sku_id', '=', 'product_skus.id')
            ->join('products', 'product_skus.product_id', '=', 'products.id')
            ->select('products.name', 'products.id as product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->whereIn('orders.payment_status', ['paid', 'settlement'])
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact(
            'startDate', 'endDate', 'totalRevenue', 'totalOrders', 'completedOrders', 'cancelledOrders', 'salesData', 'bestSellers'
        ));
    }

    public function exportCsv(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $filename = 'laporan_penjualan_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            
            // BOM for Excel UTF-8
            fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            
            fputcsv($handle, [
                'No. Invoice', 
                'Tanggal', 
                'Pelanggan', 
                'Item Barang (Kuantitas @ Harga)',
                'Tipe Pembayaran',
                'Bank',
                'Status Pesanan', 
                'Status Pembayaran', 
                'Total Transaksi (Rp)'
            ]);
            
            $orders = Order::with(['user', 'items.sku.product', 'payment'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get();
                
            foreach ($orders as $order) {
                // Compile product items list in cell
                $itemsDesc = [];
                foreach ($order->items as $item) {
                    $prodName = $item->sku->product->name ?? 'Produk';
                    $itemsDesc[] = "{$prodName} ({$item->quantity}x @ Rp " . number_format($item->price, 0, '', '') . ")";
                }
                $itemsString = implode("; ", $itemsDesc);

                fputcsv($handle, [
                    $order->invoice_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->user->name ?? 'Tamu',
                    $itemsString,
                    strtoupper(str_replace('_', ' ', $order->payment->payment_type ?? '-')),
                    strtoupper($order->payment->bank_name ?? '-'),
                    strtoupper($order->status),
                    strtoupper($order->payment_status),
                    $order->grand_total
                ]);
            }
            
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
    
    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $orders = Order::with(['user', 'items.sku.product', 'payment'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at')
            ->get();

        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['paid', 'settlement'])
            ->sum('grand_total');
            
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count();
        $cancelledOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'cancelled')->count();

        // Best Sellers (Produk Terlaris)
        $bestSellers = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_skus', 'order_items.product_sku_id', '=', 'product_skus.id')
            ->join('products', 'product_skus.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->whereIn('orders.payment_status', ['paid', 'settlement'])
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Convert Logo to base64
        $logoPath = public_path('assets/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }

        $pdf = Pdf::loadView('admin.reports.pdf_template', compact(
            'orders', 'startDate', 'endDate', 'totalRevenue', 'totalOrders', 'completedOrders', 'cancelledOrders', 'bestSellers', 'logoBase64'
        ));

        return $pdf->download('sales_report_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.pdf');
    }
}

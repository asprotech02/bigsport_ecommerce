<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { width: 100%; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo { font-size: 24px; font-weight: bold; float: left; }
        .invoice-title { float: right; font-size: 20px; font-weight: bold; color: #555; }
        .clear { clear: both; }
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background-color: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right !important; }
        .total-row { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">BAGINDO JAYA</div>
        <div class="invoice-title">INVOICE</div>
        <div class="clear"></div>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>Diterbitkan Oleh:</strong><br>
                Bagindo Jaya Tangerang<br>
                Jl. Moh. Toha No.87, RT.001/RW.004, Gerendeng,<br>
                Kec. Karawaci, Kota Tangerang, Banten 15115
            </td>
            <td width="50%" class="text-right">
                <strong>Tagihan Untuk:</strong><br>
                {{ $order->user->name }}<br>
                {{ $order->shipping_address }}<br><br>
                <strong>No. Invoice:</strong> {{ $order->invoice_number }}<br>
                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}<br>
                <strong>Status:</strong> LUNAS
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Size</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->product_size }}</td>
                <td class="text-right">Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Subtotal Produk</strong></td>
                <td class="text-right">Rp {{ number_format($order->total_product_price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right">Ongkos Kirim ({{ strtoupper($order->courier_company) }})</td>
                <td class="text-right">Rp {{ number_format($order->total_shipping_cost, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right">Diskon Promo</td>
                <td class="text-right">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" class="text-right" style="font-size: 14px;">TOTAL TAGIHAN</td>
                <td class="text-right" style="font-size: 14px;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="text-align: center; margin-top: 50px; font-size: 11px; color: #777;">
        Terima kasih telah berbelanja di Bagindo Jaya.<br>
        Ini adalah bukti pembayaran yang sah dan dicetak oleh komputer.
    </div>

</body>
</html>
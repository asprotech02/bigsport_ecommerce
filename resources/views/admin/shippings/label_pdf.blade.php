<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shipping Label - {{ $order->invoice_number }}</title>
    <style>
        @page {
            size: A6 portrait;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000000;
            margin: 10px;
            background-color: #ffffff;
        }
        .container {
            border: 2px solid #000000;
            padding: 8px;
            height: 94%;
        }
        .header {
            border-bottom: 2px dashed #000000;
            padding-bottom: 6px;
            margin-bottom: 8px;
            text-align: center;
        }
        .courier-title {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0;
        }
        .courier-service {
            font-size: 12px;
            font-weight: bold;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }
        .barcode-section {
            text-align: center;
            padding: 10px 0;
            border-bottom: 2px dashed #000000;
            margin-bottom: 8px;
        }
        .barcode-mock {
            display: inline-block;
            height: 40px;
            width: 80%;
            background: repeating-linear-gradient(
                90deg,
                #000,
                #000 2px,
                #fff 2px,
                #fff 5px,
                #000 5px,
                #000 6px,
                #fff 6px,
                #fff 8px
            );
            margin-bottom: 4px;
        }
        .resi-num {
            font-family: Courier, monospace;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 0;
        }
        .address-section {
            border-bottom: 2px dashed #000000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .address-table {
            width: 100%;
            border-collapse: collapse;
        }
        .address-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .section-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #555;
            margin-bottom: 2px;
        }
        .address-name {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .address-text {
            font-size: 9px;
        }
        .items-section {
            margin-top: 8px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .items-table th {
            border-bottom: 1px solid #000000;
            font-size: 8px;
            text-align: left;
            padding: 2px;
        }
        .items-table td {
            border-bottom: 1px dotted #ccc;
            font-size: 8px;
            padding: 3px 2px;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            border-top: 1px solid #000;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="courier-title">{{ $shipping->courier_company }}</h1>
            <div class="courier-service">{{ $shipping->courier_type }}</div>
        </div>

        <div class="barcode-section">
            <div class="barcode-mock"></div>
            <div class="resi-num">{{ $shipping->tracking_number ?? 'NO RESI' }}</div>
            <div style="font-size: 8px; margin-top: 2px;">Invoice: <strong>{{ $order->invoice_number }}</strong></div>
        </div>

        <div class="address-section">
            <table class="address-table">
                <tr>
                    <td style="width: 55%; border-right: 1px dashed #000000; padding-right: 5px;">
                        <div class="section-label">Penerima:</div>
                        @if($address)
                            <div class="address-name">{{ $address->receiver_name }}</div>
                            <div class="address-text">
                                {{ $address->receiver_phone }}<br>
                                {{ $address->full_address }}, {{ $address->village_name }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }} ({{ $address->postal_code }})
                            </div>
                        @else
                            @php
                                $parts = explode(' | ', $order->shipping_address);
                                $namePhone = $parts[0] ?? '';
                                $addrStr = $parts[1] ?? '';
                            @endphp
                            <div class="address-name">{{ $namePhone }}</div>
                            <div class="address-text">{{ $addrStr }}</div>
                        @endif
                    </td>
                    <td style="width: 45%; padding-left: 5px;">
                        <div class="section-label">Pengirim:</div>
                        <div class="address-name">BigSport Store</div>
                        <div class="address-text">
                            0812-3456-7890<br>
                            Ruko Golden Boulevard Blok C No. 12, Kel. BSD City, Kec. Serpong, Tangerang Selatan, Banten
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="items-section">
            <div class="section-label">Daftar Item:</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 60%;">Nama Produk</th>
                        <th style="width: 25%;">Detail</th>
                        <th style="width: 15%; text-align: center;">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->sku->product->name ?? 'Produk' }}</td>
                            <td>Size: {{ $item->sku->size ?? '-' }}</td>
                            <td style="text-align: center;">{{ $item->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">
            BigSport - Terima kasih atas pesanan Anda!
        </div>
    </div>
</body>
</html>

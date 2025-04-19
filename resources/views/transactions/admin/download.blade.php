<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            padding: 40px;
            color: #000;
        }
        h3 {
            margin: 0 0 10px 0;
        }
        p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f0f0f0;
        }
        th, td {
            padding: 8px 12px;
            border: none;
        }
        .bordered th, .bordered td {
            border: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-table {
            margin-top: 20px;
            width: 100%;
        }
        .summary-table td {
            padding: 6px 12px;
            text-align: right;
        }
        .summary-table td.label{
            text-align: right;
        }
        .summary-table td.value {
            text-align: right;
        }
        .summary-table td.value {
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h3>{{ $store_name }} | Telp: 085881671655 | Alamat: JL Jalak Harupat No 33</h3>

    <p>Member Status: {{ $member_status }}</p>
    <p>Phone Number: {{ $no_phone }}</p>
    <p>Joined Since: {{ $joined_since }}</p>
    <p>Member Points: {{ $point_member }}</p>

    <table class="bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td class="text-right">Rp. {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp. {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td class="label" colspan="3">Total Price</td>
            <td class="value">Rp. {{ number_format($total_price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label" colspan="3">Price After Points</td>
            <td class="value">Rp. {{ number_format($price_after_point, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label" colspan="3">Total Change</td>
            <td class="value">Rp. {{ number_format($change, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>{{ $created_at->format('Y-m-d\TH:i:s') }} | {{ $cashier }}</p>
        <p><strong>Thank you for your purchase!</strong></p>
    </div>
</body>
</html>

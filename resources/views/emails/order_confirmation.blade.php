<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation — {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { border-bottom: 2px solid #E8651A; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 22px; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #e5e7eb; }
        th { font-weight: 600; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; }
        .total-row { font-weight: 700; font-size: 16px; background: #f8fafc; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 13px; color: #64748b; }
        .btn { display: inline-block; padding: 12px 24px; background: #E8651A; color: #fff !important; text-decoration: none; border-radius: 4px; font-weight: 500; }
        .badge { display: inline-block; padding: 4px 12px; background: #d1fae5; color: #065f46; border-radius: 20px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Order Confirmation</h1>
        <p style="margin:5px 0 0; color:#64748b; font-size:13px;">Costikyan Custom Carpet — Est. 1886</p>
    </div>

    <p style="color:#64748b; font-size:14px;">Thank you for your order! We've received your request and our team will reach out within 1–2 business days.</p>

    <div style="background:#f8fafc; border-radius:8px; padding:20px; margin:20px 0;">
        <p style="margin:0; font-size:14px; color:#64748b;">Order Number</p>
        <p style="margin:4px 0 0; font-size:20px; font-weight:700; color:#0f172a;">{{ $order->order_number }}</p>
        @if($order->payment_status === 'free')
        <p style="margin:10px 0 0;"><span class="badge">FREE SAMPLE ORDER</span></p>
        @elseif($order->payment_status === 'paid')
        <p style="margin:10px 0 0;"><span class="badge">PAID</span></p>
        @endif
    </div>

    <table>
        <tr>
            <th>Item</th>
            <th style="text-align:center;">Qty</th>
            <th style="text-align:right;">Price</th>
        </tr>
        @foreach($order->items as $item)
        <tr>
            <td>
                <strong>{{ $item->product_name }}</strong>
                @if($item->size)<br><span style="font-size:13px; color:#64748b;">Size: {{ $item->size }}</span>@endif
                @if($item->color)<br><span style="font-size:13px; color:#64748b;">Color: {{ $item->color }}</span>@endif
            </td>
            <td style="text-align:center;">{{ $item->quantity }}</td>
            <td style="text-align:right; font-weight:600;">${{ number_format($item->line_total, 2) }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="2">Subtotal</td>
            <td style="text-align:right;">${{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if($order->discount > 0)
        <tr>
            <td colspan="2" style="color:#16a34a;">Discount</td>
            <td style="text-align:right; color:#16a34a;">−${{ number_format($order->discount, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td colspan="2">Shipping</td>
            <td style="text-align:right;">{{ $order->shipping > 0 ? '$'.number_format($order->shipping, 2) : 'Free' }}</td>
        </tr>
        <tr>
            <td colspan="2">Tax</td>
            <td style="text-align:right;">${{ number_format($order->tax, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td colspan="2">Total</td>
            <td style="text-align:right;">${{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    @if(!empty($shippingAddress))
    <div style="background:#f8fafc; border-radius:8px; padding:20px; margin:20px 0;">
        <p style="margin:0 0 5px; font-size:13px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Shipping Address</p>
        <p style="margin:0; font-size:14px; color:#0f172a;">
            {{ $shippingAddress['full_name'] ?? '' }}<br>
            {{ $shippingAddress['line1'] ?? '' }}<br>
            @if(!empty($shippingAddress['line2'])){{ $shippingAddress['line2'] }}<br>@endif
            {{ ($shippingAddress['city'] ?? '') . ', ' . ($shippingAddress['state'] ?? '') . ' ' . ($shippingAddress['zip'] ?? '') }}<br>
            {{ $shippingAddress['country'] ?? '' }}
        </p>
    </div>
    @endif

    <div style="text-align:center; margin:30px 0;">
        <a href="{{ $siteUrl }}/dashboard/orders" class="btn">View My Orders</a>
    </div>

    <div class="footer">
        <p><strong>Costikyan Custom Carpet</strong><br>
        {{ $address }}<br>
        {{ $phone }}<br>
        info@costikyancustomcarpet.com</p>
        <p style="font-size:12px; color:#9ca3af; margin-top:10px;">
            You received this email because you placed an order on costikyancustomcarpet.com.
            For questions, please contact us at info@costikyancustomcarpet.com or call {{ $phone }}.
        </p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Costikyan Custom Carpet Estimate</title>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { border-bottom: 2px solid #E8651A; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 22px; color: #0f172a; }
        .product-name { font-size: 18px; font-weight: 600; color: #0f172a; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #e5e7eb; }
        th { font-weight: 600; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; }
        .total-row { font-weight: 700; font-size: 16px; background: #f8fafc; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 13px; color: #64748b; }
        .btn { display: inline-block; padding: 12px 24px; background: #E8651A; color: #fff; text-decoration: none; border-radius: 4px; font-weight: 500; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/costikyan-logo.png') }}" alt="Costikyan Custom Carpet" style="height:44px; width:auto; display:block; margin-bottom:12px;">
        <h1>Your Custom Carpet Estimate</h1>
        <p style="margin:5px 0 0; color:#64748b; font-size:13px;">Costikyan Custom Carpet — Est. 1886</p>
    </div>

    <p style="color:#64748b; font-size:14px;">Thank you for your interest. Here is your personalized estimate:</p>
    @if(!empty($customer_email))
    <p style="color:#9ca3af; font-size:12px; margin-top:-6px;">Requested by: {{ $customer_email }}</p>
    @endif

    <div style="background:#f8fafc; border-radius:8px; padding:20px; margin:20px 0;">
        <p class="product-name">{{ $product->name }}</p>
        @if($product->description)
        <p style="color:#64748b; font-size:13px; margin:5px 0 0;">{{ Str::limit($product->description, 120) }}</p>
        @endif
    </div>

    <table>
        <tr>
            <th>Item</th>
            <th style="text-align:right;">Price</th>
        </tr>
        <tr>
            <td>
                <strong>{{ $product->name }}</strong><br>
                <span style="font-size:13px; color:#64748b;">Size: {{ $size }} &middot; Color: {{ $color }}</span>
            </td>
            <td style="text-align:right; font-weight:600;">${{ number_format($base_price, 0) }}</td>
        </tr>
        @if($finish && $finish !== 'N/A')
        <tr>
            <td>Finish: {{ $finish }}</td>
            <td style="text-align:right;">Included</td>
        </tr>
        @endif
        @if(!empty($add_ons))
        <tr>
            <td>
                Add-on Services<br>
                @if(!empty($add_ons['protector']))<span style="font-size:13px; color:#64748b;">Rug Protector (+$120)</span><br>@endif
                @if(!empty($add_ons['padding']))<span style="font-size:13px; color:#64748b;">Premium Padding (+$190)</span><br>@endif
                @if(!empty($add_ons['spot']))<span style="font-size:13px; color:#64748b;">Spot Kit Cleaner (+$19.99)</span>@endif
            </td>
            <td style="text-align:right; font-weight:600;">${{ number_format($add_on_price, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td>Delivery: {{ ucfirst(str_replace('-', ' ', $delivery_method)) }}</td>
            <td style="text-align:right; font-weight:600;">${{ number_format($delivery_price, 0) }}</td>
        </tr>
        <tr class="total-row">
            <td>Estimated Total</td>
            <td style="text-align:right;">${{ number_format($total, 2) }}</td>
        </tr>
    </table>

    @if($notes)
    <div style="background:#fffbeb; border-left:3px solid #EDB84A; padding:12px 15px; margin:15px 0; font-size:13px;">
        <strong>Notes:</strong> {{ $notes }}
    </div>
    @endif

    <div style="text-align:center; margin:30px 0;">
        <a href="{{ route('shop.show', $product->slug) }}" class="btn">View Product & Order</a>
    </div>

    <div class="footer">
        <p><strong>Costikyan Custom Carpet</strong><br>
        New York, NY &middot; Est. 1886</p>
        <p style="font-size:12px; color:#9ca3af; margin-top:10px;">
            This is an estimate only. Final pricing may vary based on exact specifications and current availability.
            For questions, please contact us at <a href="mailto:info@costikyan.com">info@costikyan.com</a>.
        </p>
    </div>
</body>
</html>

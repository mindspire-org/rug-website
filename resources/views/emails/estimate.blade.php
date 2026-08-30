<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Your Costikyan Custom Carpet Estimate</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin:0; padding:0; background-color:#f4f4f5; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;">

{{-- Preheader (hidden preview text) --}}
<div style="display:none; max-height:0; overflow:hidden; mso-hide:all; line-height:1px; font-size:1px; color:#f4f4f5;">
    Your personalized estimate for {{ $product->name }} is ready. Estimated total: ${{ number_format($total, 2) }}.
</div>

{{-- Outer wrapper --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;">
    <tr>
        <td align="center" style="padding:24px 12px;">

{{-- Email container --}}
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

    {{-- Header with logo --}}
    <tr>
        <td style="padding:32px 40px 24px; border-bottom:1px solid #f1f5f9;">
            <a href="https://costikyancustomcarpet.com" style="text-decoration:none; display:inline-block;">
                <img src="https://costikyancustomcarpet.com/images/costikyan-logo.png"
                     alt="Costikyan Custom Carpet"
                     width="180" height="44"
                     style="display:block; max-width:180px; height:44px; border:0; outline:none; text-decoration:none; color:#0f172a;">
            </a>
            <p style="margin:14px 0 0; font-family:Georgia,'Times New Roman',serif; font-size:13px; color:#94a3b8; letter-spacing:0.04em;">EST. 1886 &middot; NEW YORK</p>
        </td>
    </tr>

    {{-- Greeting --}}
    <tr>
        <td style="padding:28px 40px 0;">
            <h1 style="margin:0 0 8px; font-family:Georgia,'Times New Roman',serif; font-size:24px; font-weight:700; color:#0f172a; line-height:1.3;">
                Your Custom Carpet Estimate
            </h1>
            <p style="margin:0; font-family:Arial,sans-serif; font-size:15px; color:#64748b; line-height:1.6;">
                Thank you for your interest in Costikyan Custom Carpet. Below is your personalized estimate based on your selections.
            </p>
        </td>
    </tr>

    {{-- Product card --}}
    <tr>
        <td style="padding:20px 40px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;">
                <tr>
                    <td width="120" style="padding:16px; vertical-align:top;">
                        @php
                        $imageUrl = $product->primaryImage
                            ? 'https://costikyancustomcarpet.com/media/products/' . basename($product->primaryImage->path)
                            : 'https://costikyancustomcarpet.com/images/costikyan-logo.png';
                        @endphp
                        <a href="{{ route('shop.show', $product->slug) }}" style="text-decoration:none; display:block;">
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $product->name }}"
                                 width="100" height="100"
                                 style="display:block; width:100px; height:100px; border-radius:8px; object-fit:cover; border:0; color:#0f172a;">
                        </a>
                    </td>
                    <td style="padding:16px 16px 16px 0; vertical-align:middle;">
                        <p style="margin:0 0 4px; font-family:Arial,sans-serif; font-size:17px; font-weight:700; color:#0f172a;">{{ $product->name }}</p>
                        @if($product->description)
                        <p style="margin:0; font-family:Arial,sans-serif; font-size:13px; color:#64748b; line-height:1.5;">{{ Str::limit($product->description, 100) }}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Estimate breakdown --}}
    <tr>
        <td style="padding:20px 40px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2" style="padding:0 0 10px; font-family:Arial,sans-serif; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.06em; border-bottom:2px solid #0f172a;">
                        Estimate Breakdown
                    </td>
                </tr>

                {{-- Base product --}}
                <tr>
                    <td style="padding:14px 0; border-bottom:1px solid #f1f5f9; font-family:Arial,sans-serif; font-size:14px; color:#334155;">
                        <strong>{{ $product->name }}</strong><br>
                        <span style="font-size:12px; color:#94a3b8;">Size: {{ $size }} &middot; Color: {{ $color }}</span>
                    </td>
                    <td align="right" style="padding:14px 0; border-bottom:1px solid #f1f5f9; font-family:Arial,sans-serif; font-size:14px; font-weight:600; color:#0f172a; white-space:nowrap;">
                        ${{ number_format($base_price, 0) }}
                    </td>
                </tr>

                {{-- Finish --}}
                @if($finish && $finish !== 'N/A')
                <tr>
                    <td style="padding:12px 0; border-bottom:1px solid #f1f5f9; font-family:Arial,sans-serif; font-size:14px; color:#334155;">
                        Finish: {{ $finish }}
                    </td>
                    <td align="right" style="padding:12px 0; border-bottom:1px solid #f1f5f9; font-family:Arial,sans-serif; font-size:14px; color:#64748b; white-space:nowrap;">
                        Included
                    </td>
                </tr>
                @endif

                {{-- Add-ons --}}
                @if(!empty($add_ons) && ($add_ons['protector'] ?? false || $add_ons['padding'] ?? false || $add_ons['spot'] ?? false))
                <tr>
                    <td style="padding:12px 0; border-bottom:1px solid #f1f5f9; font-family:Arial,sans-serif; font-size:14px; color:#334155;">
                        Add-on Services
                        @if(!empty($add_ons['protector']))<br><span style="font-size:12px; color:#94a3b8;">&bull; Rug Protector (+$120)</span>@endif
                        @if(!empty($add_ons['padding']))<br><span style="font-size:12px; color:#94a3b8;">&bull; Premium Padding (+$190)</span>@endif
                        @if(!empty($add_ons['spot']))<br><span style="font-size:12px; color:#94a3b8;">&bull; Spot Kit Cleaner (+$19.99)</span>@endif
                    </td>
                    <td align="right" style="padding:12px 0; border-bottom:1px solid #f1f5f9; font-family:Arial,sans-serif; font-size:14px; font-weight:600; color:#0f172a; white-space:nowrap;">
                        ${{ number_format($add_on_price, 2) }}
                    </td>
                </tr>
                @endif

                {{-- Delivery --}}
                <tr>
                    <td style="padding:12px 0; border-bottom:1px solid #f1f5f9; font-family:Arial,sans-serif; font-size:14px; color:#334155;">
                        Delivery: {{ ucfirst(str_replace('-', ' ', $delivery_method)) }}
                    </td>
                    <td align="right" style="padding:12px 0; border-bottom:1px solid #f1f5f9; font-family:Arial,sans-serif; font-size:14px; font-weight:600; color:#0f172a; white-space:nowrap;">
                        @if($delivery_price == 0)
                        Free
                        @else
                        ${{ number_format($delivery_price, 0) }}
                        @endif
                    </td>
                </tr>

                {{-- Total --}}
                <tr>
                    <td style="padding:16px 0 4px; font-family:Arial,sans-serif; font-size:16px; font-weight:700; color:#0f172a;">
                        Estimated Total
                    </td>
                    <td align="right" style="padding:16px 0 4px; font-family:Georgia,'Times New Roman',serif; font-size:22px; font-weight:700; color:#E8651A; white-space:nowrap;">
                        ${{ number_format($total, 2) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Notes --}}
    @if($notes)
    <tr>
        <td style="padding:16px 40px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fffbeb; border-left:3px solid #EDB84A; border-radius:0 8px 8px 0;">
                <tr>
                    <td style="padding:14px 16px; font-family:Arial,sans-serif; font-size:13px; color:#78350f; line-height:1.6;">
                        <strong>Your Notes:</strong><br>{{ $notes }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    {{-- CTA Button --}}
    <tr>
        <td align="center" style="padding:28px 40px 8px;">
            <a href="{{ route('shop.show', $product->slug) }}"
               style="display:inline-block; padding:14px 36px; background-color:#E8651A; color:#ffffff; font-family:Arial,sans-serif; font-size:15px; font-weight:600; text-decoration:none; border-radius:8px; letter-spacing:0.02em;">
                View Product &amp; Place Order
            </a>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding:0 40px 24px;">
            <p style="margin:0; font-family:Arial,sans-serif; font-size:12px; color:#94a3b8;">
                or call us at {{ $phone ?? '800-247-7847' }}
            </p>
        </td>
    </tr>

    {{-- Divider --}}
    <tr>
        <td style="padding:0 40px;">
            <hr style="border:0; border-top:1px solid #e2e8f0; margin:0;">
        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="padding:24px 40px 32px;">
            <p style="margin:0 0 10px; font-family:Georgia,'Times New Roman',serif; font-size:15px; font-weight:700; color:#0f172a;">
                Costikyan Custom Carpet
            </p>
            <p style="margin:0 0 6px; font-family:Arial,sans-serif; font-size:13px; color:#64748b; line-height:1.6;">
                {{ $address ?? '37-11 48th Avenue, Long Island City, NY 11101' }}<br>
                {{ $phone ?? '800-247-7847' }} &middot; <a href="mailto:info@costikyancustomcarpet.com" style="color:#E8651A; text-decoration:none;">info@costikyancustomcarpet.com</a>
            </p>
            <p style="margin:12px 0 0; font-family:Arial,sans-serif; font-size:11px; color:#94a3b8; line-height:1.6;">
                This estimate was prepared for {{ $customer_email }} and is valid for 30 days. Final pricing may vary based on exact specifications and current availability.<br>
                You received this email because you requested an estimate on costikyancustomcarpet.com.
            </p>
        </td>
    </tr>

</table>{{-- /email container --}}

{{-- Footer note --}}
<p style="margin:20px 0 0; font-family:Arial,sans-serif; font-size:11px; color:#94a3b8; text-align:center; line-height:1.6;">
    Costikyan Custom Carpet &middot; Est. 1886 &middot; New York, NY
</p>

</td>
</tr>
</table>{{-- /outer wrapper --}}

</body>
</html>

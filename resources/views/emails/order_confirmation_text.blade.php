Costikyan Custom Carpet — Est. 1886
=====================================

Order Confirmation
Order Number: {{ $order->order_number }}

Thank you for your order! We've received your request
and our team will reach out within 1-2 business days.

@if($order->payment_status === 'free')
** FREE SAMPLE ORDER **
@elseif($order->payment_status === 'paid')
** PAID **
@endif

-----------------------------------------------
ORDER ITEMS
-----------------------------------------------
@foreach($order->items as $item)
{{ $item->product_name }}
  Size: {{ $item->size ?? 'N/A' }}
  Color: {{ $item->color ?? 'N/A' }}
  Qty: {{ $item->quantity }}
  Price: ${{ number_format($item->line_total, 2) }}

@endforeach
-----------------------------------------------
Subtotal:                     ${{ number_format($order->subtotal, 2) }}
@if($order->discount > 0)
Discount:                    -${{ number_format($order->discount, 2) }}
@endif
Shipping:                     {{ $order->shipping > 0 ? '$'.number_format($order->shipping, 2) : 'Free' }}
Tax:                          ${{ number_format($order->tax, 2) }}
-----------------------------------------------
TOTAL:                        ${{ number_format($order->total, 2) }}
-----------------------------------------------

@if(!empty($shippingAddress))
SHIPPING ADDRESS:
{{ $shippingAddress['full_name'] ?? '' }}
{{ $shippingAddress['line1'] ?? '' }}
@if(!empty($shippingAddress['line2'])){{ $shippingAddress['line2'] }}
@endif{{ ($shippingAddress['city'] ?? '') . ', ' . ($shippingAddress['state'] ?? '') . ' ' . ($shippingAddress['zip'] ?? '') }}
{{ $shippingAddress['country'] ?? '' }}
@endif

-----------------------------------------------

View your orders online:
{{ $siteUrl }}/dashboard/orders

-----------------------------------------------

Costikyan Custom Carpet
{{ $address }}
{{ $phone }}
info@costikyancustomcarpet.com

You received this email because you placed an order
on costikyancustomcarpet.com. For questions, please
contact us at info@costikyancustomcarpet.com or
call {{ $phone }}.

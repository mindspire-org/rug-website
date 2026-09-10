Costikyan Custom Carpet — Est. 1886
=====================================

Your Custom Carpet Estimate

Thank you for your interest in Costikyan Custom Carpet.
Below is your personalized estimate based on your selections.

PRODUCT: {{ $product->name }}
{{ $product->description ? Str::limit($product->description, 100) : '' }}

-----------------------------------------------
ESTIMATE BREAKDOWN
-----------------------------------------------

{{ $product->name }} (Size: {{ $size }}, Color: {{ $color }})
                              ${{ number_format($base_price, 0) }}

@if($finish && $finish !== 'N/A')
Finish: {{ $finish }}
                              Included
@endif

@if(!empty($add_ons) && ($add_ons['protector'] ?? false || $add_ons['padding'] ?? false || $add_ons['spot'] ?? false))
Add-on Services:
  - Rug Protector (+$120)@if(!empty($add_ons['protector'])) [SELECTED]@endif
  - Premium Padding (+$190)@if(!empty($add_ons['padding'])) [SELECTED]@endif
  - Spot Kit Cleaner (+$19.99)@if(!empty($add_ons['spot'])) [SELECTED]@endif
                              ${{ number_format($add_on_price, 2) }}
@endif

Delivery: {{ ucfirst(str_replace('-', ' ', $delivery_method)) }}
@if($delivery_price == 0)                              Free
@else                              ${{ number_format($delivery_price, 0) }}
@endif

-----------------------------------------------
ESTIMATED TOTAL:              ${{ number_format($total, 2) }}
-----------------------------------------------

@if($notes)
Your Notes: {{ $notes }}
@endif

View this product online:
{{ route('shop.show', $product->slug) }}

-----------------------------------------------

Costikyan Custom Carpet
{{ $address ?? '37-11 48th Avenue, Long Island City, NY 11101' }}
{{ $phone ?? '800-247-7847' }}
info@costikyancustomcarpet.com

This estimate was prepared for {{ $customer_email }} and is valid
for 30 days. Final pricing may vary based on exact specifications
and current availability.

You received this email because you requested an estimate on
costikyancustomcarpet.com.

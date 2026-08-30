@extends('layouts.site')
@section('title', 'Terms & Conditions')

@section('content')
@php
$sections = [
    ['1. Use of the Website', [
        'Costikyan Custom Carpets ("Costikyan," "we," "us," or "our") operates this website, costikyancustomcarpet.com (the "Website"). These Terms & Conditions ("Terms") govern your access to and use of the Website, including our online store, products, services, customer accounts, shopping cart, and other features.',
        'By accessing or using the Website, you agree to these Terms. If you do not agree with these Terms, please do not use the Website.',
        'You may use the Website only for lawful purposes and in accordance with these Terms.',
        'You agree not to:',
        ['Use the Website for any unlawful or fraudulent purpose.', 'Attempt to gain unauthorized access to the Website or its systems.', 'Interfere with or disrupt the Website or its operation.', 'Introduce viruses, malware, or other harmful code.', 'Use automated systems to scrape, copy, or collect Website content without our permission.', 'Misrepresent your identity or affiliation.', 'Use another person\'s account without authorization.', 'Reproduce, copy, modify, distribute, or exploit Website content except as permitted by these Terms or applicable law.'],
        'We reserve the right to suspend or terminate access to the Website if we reasonably believe that you have violated these Terms or applicable law.',
    ]],
    ['2. Website Content', [
        'The Website and its contents, including text, photographs, images, graphics, logos, designs, product descriptions, videos, artwork, catalogs, and other materials, are provided for informational and commercial purposes.',
        'We make reasonable efforts to ensure that information on the Website is accurate and current. However, we do not guarantee that all Website content is complete, accurate, current, or free from errors.',
        'Product images and colors displayed on your device may vary from the actual product due to screen settings, photography, lighting, materials, dye lots, natural variations, and other factors.',
        'We reserve the right to correct errors, inaccuracies, or omissions and to update Website content at any time without prior notice.',
    ]],
    ['3. Products and Availability', [
        'We offer rugs, carpets, samples, custom products, and related goods and services through the Website.',
        'Product availability may change without notice. We reserve the right to limit quantities, discontinue products, or refuse an order where permitted by law.',
        'Some products may be made to order, customized, fabricated, or otherwise specially produced based on customer specifications.',
        'Custom or made-to-order products may differ in appearance from photographs, samples, renderings, or previous orders due to variations in materials, dye lots, manufacturing processes, handmade construction, and other factors.',
    ]],
    ['4. Product Descriptions, Colors, and Measurements', [
        'We make reasonable efforts to accurately describe our products. However, handmade, natural, and custom products may contain variations in color, texture, pattern, pile, dimensions, construction, and appearance.',
        'Actual colors may vary depending on lighting, materials, photography, monitors, mobile devices, and other factors.',
        'Dimensions stated on the Website may be approximate where products are handmade, custom-made, or subject to manufacturing tolerances.',
        'Where exact specifications are important to a project, customers should contact Costikyan before placing an order.',
    ]],
    ['5. Custom and Special-Order Products', [
        'Custom, personalized, made-to-order, or specially fabricated products may be subject to different cancellation, return, exchange, or refund terms.',
        'Unless otherwise required by law or expressly agreed to by Costikyan in writing, custom or specially ordered products may not be eligible for return or cancellation once production or fabrication has begun.',
        'Customers are responsible for reviewing and approving applicable specifications, dimensions, colors, materials, designs, and other project details before production.',
    ]],
    ['6. Orders and Acceptance', [
        'Submitting an order through the Website constitutes an offer to purchase the selected products.',
        'Receipt of an order does not necessarily mean that Costikyan has accepted the order. We reserve the right to accept, decline, cancel, or limit an order for any lawful reason.',
        'We may contact you if additional information is required to process your order.',
        'If we cancel an order after payment has been processed, we will provide an appropriate refund of amounts actually paid for the canceled portion of the order, subject to applicable law.',
    ]],
    ['7. Pricing', [
        'Prices displayed on the Website are subject to change without notice.',
        'We make reasonable efforts to display accurate pricing, but errors may occur. If a product is listed at an incorrect price due to an error, we reserve the right to correct the price and, where appropriate, contact you before processing the order.',
        'Prices may not include applicable taxes, shipping charges, delivery charges, installation charges, customs duties, or other charges unless expressly stated.',
    ]],
    ['8. Taxes', [
        'Applicable sales tax and other governmental charges may be added to purchases as required by applicable law.',
        'The amount of tax charged may depend on the shipping or delivery location and other factors.',
    ]],
    ['9. Payment', [
        'Payment for online purchases may be processed through third-party payment processors.',
        'You represent that you are authorized to use the payment method provided for an order.',
        'You agree to provide accurate and complete billing information.',
        'Costikyan reserves the right to decline or cancel transactions where permitted by law, including where there is reason to suspect fraud, unauthorized activity, pricing errors, or other improper activity.',
    ]],
    ['10. Shipping and Delivery', [
        'Shipping and delivery availability, costs, timing, and methods may vary depending on the product, destination, availability, and other circumstances.',
        'Estimated delivery dates are estimates only and are not guaranteed unless expressly agreed to in writing.',
        'Costikyan is not responsible for delays caused by circumstances beyond our reasonable control, including carrier delays, weather, natural disasters, labor disruptions, supply shortages, customs delays, governmental actions, or other events outside our reasonable control.',
        'You are responsible for providing accurate shipping and delivery information.',
        'Additional charges may apply for special delivery requirements, installation, stairs, difficult access, storage, redelivery, or other services where applicable.',
    ]],
    ['11. Returns, Refunds, and Cancellations', [
        'Returns, refunds, exchanges, and cancellations are subject to the terms communicated to you at the time of purchase and any applicable policies provided by Costikyan.',
        'Custom, personalized, made-to-order, and specially fabricated products may be subject to different cancellation, return, exchange, or refund restrictions.',
        'If you have questions about whether a particular product or order is eligible for return, cancellation, exchange, or refund, please contact Costikyan before placing your order.',
        'Nothing in these Terms limits any rights or remedies that cannot legally be waived under applicable law.',
    ]],
    ['12. Samples', [
        'Samples may be provided for informational purposes and may not exactly match the final product.',
        'Variations may occur due to dye lots, materials, handmade construction, manufacturing processes, lighting, aging, and other factors.',
        'Where a sample is provided for a custom or made-to-order project, customers should understand that the final product may contain reasonable variations.',
    ]],
    ['13. Accounts', [
        'Certain Website features may require you to create an account.',
        'You are responsible for maintaining the confidentiality of your account credentials and for activities conducted through your account.',
        'You agree to provide accurate and current information and to promptly update information that changes.',
        'You should notify Costikyan if you believe your account has been accessed or used without authorization.',
        'We reserve the right to suspend or terminate accounts where permitted by law.',
    ]],
    ['14. Intellectual Property', [
        'The Website and its contents are owned by Costikyan or its licensors and are protected by applicable copyright, trademark, trade dress, and other intellectual property laws.',
        'Costikyan\'s names, logos, trademarks, product names, designs, photographs, artwork, and other branding may not be used without prior written permission.',
        'Except as expressly permitted by these Terms or applicable law, you may not copy, reproduce, modify, distribute, publicly display, sell, license, or otherwise exploit Website content without our prior written consent.',
    ]],
    ['15. Customer-Submitted Content', [
        'If you submit photographs, drawings, plans, reviews, comments, testimonials, project information, or other materials to Costikyan, you represent that you have the right to provide those materials and that doing so does not violate the rights of another person.',
        'By submitting materials for publication or promotional use, you grant Costikyan a non-exclusive, royalty-free right to use, reproduce, display, and distribute those materials for the purposes for which you provided them, subject to our Privacy Policy and applicable law.',
        'We are not required to publish or use submitted content and may remove content at our discretion.',
    ]],
    ['16. Third-Party Services and Links', [
        'The Website may contain links to third-party websites, services, payment processors, shipping providers, social-media platforms, or other resources.',
        'Costikyan does not control and is not responsible for the content, availability, privacy practices, security, or terms of third-party websites or services.',
        'Your use of third-party services may be subject to separate terms and policies.',
    ]],
    ['17. Disclaimer of Warranties', [
        'To the maximum extent permitted by applicable law, the Website and its content are provided on an "as is" and "as available" basis.',
        'Costikyan does not guarantee that:',
        ['The Website will always be available or uninterrupted.', 'The Website will be error-free.', 'Website content will always be complete, accurate, or current.', 'Defects will always be corrected.', 'The Website will be free of viruses or other harmful components.'],
        'Nothing in these Terms excludes any warranty or consumer protection right that cannot legally be excluded.',
    ]],
    ['18. Limitation of Liability', [
        'To the maximum extent permitted by applicable law, Costikyan and its owners, officers, employees, affiliates, contractors, service providers, and agents will not be liable for indirect, incidental, special, consequential, exemplary, or punitive damages arising out of or related to your use of the Website or purchase or use of products or services, except to the extent such limitation is prohibited by law.',
        'To the maximum extent permitted by applicable law, Costikyan\'s total liability arising from a transaction or your use of the Website will be limited to the amount you actually paid to Costikyan for the applicable product or service giving rise to the claim.',
        'Nothing in these Terms limits liability that cannot legally be limited or excluded under applicable law.',
    ]],
    ['19. Indemnification', [
        'To the maximum extent permitted by applicable law, you agree to indemnify and hold harmless Costikyan and its owners, officers, employees, affiliates, contractors, service providers, and agents from claims, liabilities, damages, losses, and expenses arising from your unlawful use of the Website, violation of these Terms, or violation of another person\'s rights.',
    ]],
    ['20. Force Majeure', [
        'Costikyan will not be responsible for delays or failures caused by circumstances beyond our reasonable control, including natural disasters, severe weather, fire, flood, epidemic or pandemic, war, terrorism, civil disturbance, labor disputes, transportation disruptions, supply-chain interruptions, governmental actions, utility failures, internet or telecommunications failures, or other events beyond our reasonable control.',
    ]],
    ['21. Governing Law', [
        'These Terms will be governed by the laws of the State of New York, without regard to its conflict-of-laws principles, except to the extent that applicable law requires otherwise.',
        'Nothing in these Terms is intended to deprive consumers of rights or protections that cannot legally be waived under the laws applicable to their place of residence.',
    ]],
    ['22. Disputes', [
        'Before initiating formal legal proceedings concerning a dispute arising from these Terms or your use of the Website, you agree to first contact Costikyan and provide a reasonable opportunity to resolve the matter informally.',
        'Nothing in this section prevents either party from seeking emergency or equitable relief where appropriate or from exercising rights that cannot legally be waived.',
    ]],
    ['23. Severability', [
        'If any provision of these Terms is determined to be unlawful, invalid, or unenforceable, that provision will be enforced to the maximum extent permitted by law, and the remaining provisions will remain in full force and effect.',
    ]],
    ['24. Waiver', [
        'Costikyan\'s failure to enforce any provision of these Terms does not constitute a waiver of that provision or our right to enforce it later.',
    ]],
    ['25. Changes to These Terms', [
        'We may update these Terms from time to time to reflect changes in our business, Website, products, services, or applicable law.',
        'When we make changes, we will update the "Last Updated" date at the top of these Terms.',
        'Your continued use of the Website after updated Terms become effective constitutes acceptance of the revised Terms, to the extent permitted by law.',
    ]],
    ['26. Entire Agreement', [
        'These Terms, together with any policies or terms expressly incorporated by reference, constitute the agreement between you and Costikyan concerning your use of the Website, except where separate written terms apply to a particular product, service, order, or transaction.',
    ]],
];
@endphp
<div class="max-w-3xl mx-auto px-6 py-16">
    <h1 style="font-family:'Lusitana',serif; font-size:36px; font-weight:700; color:#121212;" class="mb-2">Terms &amp; Conditions</h1>
    <p style="font-size:13px; color:rgba(18,18,18,0.5);" class="mb-8">Effective Date: August 18, 2026 &middot; Last Updated: August 18, 2026</p>

    <div style="font-size:14px; color:rgba(18,18,18,0.75); line-height:1.8;" class="space-y-6">
        @foreach($sections as [$heading, $blocks])
        <div>
            <h2 style="font-family:'Lusitana',serif; font-size:19px; font-weight:700; color:#121212;" class="mb-2">{{ $heading }}</h2>
            @foreach($blocks as $block)
                @if(is_array($block))
                <ul style="list-style:disc; padding-left:22px; margin:6px 0;">
                    @foreach($block as $li)<li style="margin:3px 0;">{{ $li }}</li>@endforeach
                </ul>
                @else
                <p class="mb-2">{{ $block }}</p>
                @endif
            @endforeach
        </div>
        @endforeach

        <div>
            <h2 style="font-family:'Lusitana',serif; font-size:19px; font-weight:700; color:#121212;" class="mb-2">27. Contact Us</h2>
            <p>If you have questions about these Terms or the Website, please contact:</p>
            <p style="margin-top:8px;">
                <strong>Costikyan Custom Carpets</strong><br>
                37-11 48th Avenue<br>
                Long Island City, NY 11101<br>
                Email: <a href="mailto:info@costikyancustomcarpet.com" style="color:#E8651A;">info@costikyancustomcarpet.com</a><br>
                Telephone: 800-247-7847
            </p>
        </div>

        <p style="font-size:12px; color:rgba(18,18,18,0.45); border-top:1px solid rgba(18,18,18,0.1); padding-top:16px;">&copy; 2026 Costikyan Custom Carpets. All Rights Reserved.</p>
    </div>
</div>
@endsection

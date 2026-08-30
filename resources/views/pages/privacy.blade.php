@extends('layouts.site')
@section('title', 'Privacy Policy')

@section('content')
@php
$sections = [
    ['1. Information We Collect', [
        'We may collect personal information that you voluntarily provide to us, including:',
        ['Name', 'Email address', 'Telephone or mobile number', 'Mailing, billing, shipping, or service address', 'Company or business name', 'Designer, architect, dealer, or trade affiliation', 'Information submitted through contact, quote, sample, trade, or other forms', 'Communications you send to us', 'Project information, including measurements, specifications, photographs, drawings, plans, and design preferences', 'Product, order, purchase, shipping, delivery, installation, service, and transaction information', 'Information associated with a customer account', 'Saved products, preferences, or similar website activity', 'Information provided when you sign up for our marketing programs or other communications', 'Payment-related information when applicable'],
        'We may also automatically collect certain information when you visit or use our website, including:',
        ['IP address', 'Browser and device information', 'Operating system', 'Pages and products visited or viewed', 'Referring website', 'Date and time of visits', 'General geographic information derived from an IP address', 'Website interaction and usage information', 'Information concerning interactions with our emails, advertisements, or other communications, where applicable'],
        'If you submit photographs, drawings, floor plans, architectural plans, or other project materials to us, those materials may contain personal information. We may use such materials as reasonably necessary to provide requested products or services and manage your project.',
    ]],
    ['2. How We Use Your Information', [
        'We may use personal information to:',
        ['Respond to inquiries and requests for information', 'Prepare estimates, proposals, and quotations', 'Provide custom carpet, rug, fabrication, installation, cleaning, restoration, storage, delivery, and related services', 'Process and manage orders and purchases', 'Process payments and facilitate shipping and delivery', 'Create and administer customer accounts', 'Maintain saved products, preferences, or similar website functionality', 'Communicate regarding projects, appointments, deliveries, installations, purchases, and services', 'Provide samples and product information', 'Administer designer, architect, dealer, and trade programs', 'Provide customer service and support', 'Operate, maintain, and improve our website', 'Improve our products, services, and customer experience', 'Analyze website traffic, usage, and customer interactions', 'Maintain customer, business, order, and transaction records', 'Detect, investigate, and prevent fraud, security incidents, and unauthorized activity', 'Protect our rights, property, employees, customers, and business operations', 'Comply with applicable laws and legal obligations', 'Send transactional, administrative, and service-related communications', 'Send promotional or marketing communications where permitted by law and, where required, with appropriate consent', 'Perform other activities reasonably necessary to operate our business and provide our products and services'],
    ]],
    ['3. Email and Text Message Communications', [
        'If you provide your email address or mobile telephone number, we may use it to communicate with you regarding inquiries, estimates, orders, appointments, deliveries, services, purchases, account activity, and other matters related to your relationship with Costikyan.',
        'Where permitted by applicable law and where any required consent has been obtained, we may also send promotional emails or text messages concerning our products, services, events, special offers, new collections, or other information we believe may be of interest to you.',
        'You may unsubscribe from promotional emails by using the unsubscribe link contained in the email.',
        'For promotional text messages, you may opt out by replying STOP or by following the opt-out instructions contained in the message. Message and data rates may apply. Consent to receive marketing text messages is not a condition of purchasing any goods or services.',
        'We may use third-party service providers to assist us with email, text messaging, and other communications. Those providers may process personal information on our behalf.',
    ]],
    ['4. Cookies and Website Technologies', [
        'Our website may use cookies, pixels, tags, scripts, analytics technologies, and similar technologies to operate the website, maintain account and shopping functionality, remember preferences, understand how visitors use the website, measure marketing effectiveness, and improve the user experience.',
        'These technologies may collect information concerning your device, browser, IP address, website activity, products viewed, shopping activity, and interactions with our advertisements or communications.',
        'Third-party analytics, advertising, hosting, e-commerce, or technology providers may also collect information through these technologies when used on our website.',
        'You may be able to control or disable certain cookies through your browser settings or through cookie preference tools provided on our website. Disabling cookies may affect the functionality of certain portions of the website, including account, saved-item, shopping-cart, or other features.',
    ]],
    ['5. Analytics and Advertising Services', [
        'We may use third-party analytics, advertising, marketing, e-commerce, hosting, and technology services to operate the website, understand website usage, measure marketing performance, improve our services, and communicate with customers.',
        'These providers may use cookies or similar technologies to collect information about website visits, interactions, products viewed, and advertising activity.',
        'Where required by applicable law, we will provide legally required disclosures and choices concerning certain targeted advertising, sale, sharing, or other processing of personal information.',
    ]],
    ['6. Online Accounts and Purchases', [
        'Our website may allow users to create accounts, save products or preferences, maintain account information, add products to a shopping cart, and purchase products.',
        'If you create an account or make a purchase, we may collect information necessary to create and administer the account, process the transaction, fulfill orders, provide customer service, and maintain appropriate business records.',
        'Payment transactions may be processed by third-party payment processors. We may receive information such as payment status, transaction information, billing information, or other information necessary to complete or document a transaction.',
    ]],
    ['7. How We Share Information', [
        'We do not sell personal information for money.',
        'We may disclose personal information to third parties when reasonably necessary to operate our business, provide our products and services, complete transactions, or perform other legitimate business activities, including:',
        ['Website hosting and technology providers', 'E-commerce and account-management providers', 'Customer relationship management providers', 'Email and communications providers', 'Payment processors', 'Shipping, delivery, and logistics providers', 'Installation, fabrication, cleaning, restoration, and service providers', 'Manufacturers, suppliers, dealers, or contractors involved in fulfilling customer orders or projects', 'Marketing and advertising providers', 'Analytics providers', 'Professional advisers, including attorneys, accountants, insurers, and consultants', 'Other vendors and contractors performing services on our behalf'],
        'We may also disclose information when required by law, legal process, court order, governmental request, or when we reasonably believe disclosure is necessary to protect our rights, property, customers, employees, or others.',
        'If Costikyan or a related business is involved in a merger, acquisition, financing, reorganization, sale of assets, or similar business transaction, personal information may be transferred as part of that transaction.',
        'We may also disclose information with your direction or consent or as otherwise permitted by applicable law.',
    ]],
    ['8. Payment Information', [
        'When payment information is collected electronically, payment transactions may be processed by third-party payment processors.',
        'We do not intend to store complete credit or debit card information on our website unless specifically required for a legitimate business purpose and handled using appropriate security measures.',
        'Payment processors may maintain their own privacy and security practices. Their handling of payment information may be governed by their own privacy policies and terms.',
    ]],
    ['9. Data Security', [
        'We use reasonable administrative, technical, and physical safeguards designed to protect personal information from unauthorized access, loss, misuse, alteration, or disclosure.',
        'However, no website, electronic transmission, or information storage system can be guaranteed to be completely secure. Accordingly, we cannot guarantee the absolute security of information transmitted to or stored by us.',
    ]],
    ['10. Data Retention', [
        'We may retain personal information for as long as reasonably necessary to fulfill the purposes for which it was collected, maintain customer and business records, provide ongoing services, process transactions, resolve disputes, enforce agreements, and satisfy legal, accounting, tax, insurance, or regulatory requirements.',
        'Different categories of information may be retained for different periods depending on the nature of the information and the purposes for which it is maintained.',
    ]],
    ['11. Sources of Personal Information', [
        'We may obtain personal information from:',
        ['You directly', 'Your use of our website', 'Your communications and transactions with us', 'Designers, architects, dealers, trade professionals, or other business contacts', 'Service providers or contractors working with or on behalf of Costikyan', 'Other individuals or businesses acting on your behalf', 'Publicly available sources', 'Other sources where permitted by applicable law'],
    ]],
    ['12. Your Privacy Choices and Rights', [
        'Depending on where you reside and subject to applicable law, you may have certain rights concerning your personal information. These rights may include the right to:',
        ['Request information about personal information we maintain about you', 'Request access to certain personal information', 'Request correction of inaccurate personal information', 'Request deletion of certain personal information', 'Opt out of certain marketing communications', 'Opt out of certain targeted advertising or other processing where required by applicable law', 'Opt out of certain sale or sharing of personal information where applicable', 'Exercise other privacy rights provided by applicable state or federal law'],
        'These rights are subject to applicable legal exceptions, limitations, and verification requirements.',
        'To submit a privacy request, please contact us using the information provided below.',
        'We may take reasonable steps to verify your identity before completing certain privacy requests.',
        'We will not discriminate against you for exercising privacy rights provided by applicable law.',
    ]],
    ['13. Children\'s Privacy', [
        'Our website and services are intended for adults and are not directed toward children under 13 years of age.',
        'We do not knowingly collect personal information from children under 13. If we learn that we have collected personal information from a child under 13 without appropriate authorization, we will take reasonable steps to delete it.',
    ]],
    ['14. Third-Party Websites', [
        'Our website may contain links to websites or services operated by third parties, including websites operated by related or affiliated businesses.',
        'Costikyan is not responsible for the privacy, security, content, or practices of third-party websites or services.',
        'We encourage you to review the privacy policies of any third-party websites or services you visit.',
    ]],
    ['15. Do Not Track and Opt-Out Signals', [
        'Some web browsers provide a "Do Not Track" signal or similar mechanism. Because there is not a universally accepted standard governing how websites should respond to these signals, our website may not respond to all such signals.',
        'Where required by applicable law, we will honor applicable legally recognized browser-based opt-out preference signals.',
    ]],
    ['16. Changes to This Privacy Policy', [
        'We may modify this Privacy Policy periodically to reflect changes in our business practices, technology, services, or applicable laws.',
        'When we make changes, we will update the "Last Updated" date at the top of this Privacy Policy. Material changes may also be communicated through the website or by other appropriate means.',
        'Your continued use of the website after an updated Privacy Policy becomes effective constitutes acknowledgment of the revised policy.',
    ]],
];
@endphp
<div class="max-w-3xl mx-auto px-6 py-16">
    <h1 style="font-family:'Lusitana',serif; font-size:36px; font-weight:700; color:#121212;" class="mb-2">Privacy Policy</h1>
    <p style="font-size:13px; color:rgba(18,18,18,0.5);" class="mb-8">Effective Date: August 14, 2026 &middot; Last Updated: August 14, 2026</p>

    <div style="font-size:14px; color:rgba(18,18,18,0.75); line-height:1.8;" class="space-y-6">
        <p>Costikyan Custom Carpets ("Costikyan," "we," "us," or "our") respects your privacy and is committed to protecting your personal information. This Privacy Policy explains how we collect, use, disclose, and protect personal information when you visit or use our website, communicate with us, request information or a quote, request samples, create or use an account, purchase products, or otherwise interact with us.</p>
        <p>By using our website, you acknowledge the practices described in this Privacy Policy.</p>

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
            <h2 style="font-family:'Lusitana',serif; font-size:19px; font-weight:700; color:#121212;" class="mb-2">17. Contact Us</h2>
            <p>If you have questions about this Privacy Policy, our privacy practices, or would like to submit a privacy-related request, please contact:</p>
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

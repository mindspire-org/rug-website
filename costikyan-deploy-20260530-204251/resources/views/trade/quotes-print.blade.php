<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quote #{{ $quote->quote_number }} — Costikyan Trade</title>
    <link href="https://fonts.googleapis.com/css2?family=Lusitana:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; font-size: 13px; color: #121212; background: #fff; line-height: 1.5; }
        .page { max-width: 720px; margin: 40px auto; padding: 0 40px; }
        .header { border-bottom: 2px solid #121212; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-family: 'Lusitana', serif; font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .header p { font-size: 12px; color: rgba(18,18,18,0.55); }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 30px; }
        .meta-block h3 { font-size: 10px; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(18,18,18,0.45); margin-bottom: 6px; }
        .meta-block p { font-size: 13px; color: #121212; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { text-align: left; font-size: 10px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: rgba(18,18,18,0.45); padding: 10px 0; border-bottom: 1px solid rgba(18,18,18,0.12); }
        td { padding: 12px 0; border-bottom: 1px solid rgba(18,18,18,0.06); font-size: 13px; }
        .total-row td { border-top: 2px solid #121212; border-bottom: none; font-weight: 600; font-size: 14px; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(18,18,18,0.1); font-size: 11px; color: rgba(18,18,18,0.45); text-align: center; }
        @media print {
            .no-print { display: none; }
            .page { margin: 0; padding: 0; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="no-print" style="text-align:right; margin-bottom:16px;">
            <button onclick="window.print()" style="padding:8px 16px; background:#121212; color:#fff; border:none; border-radius:4px; font-size:12px; font-weight:500; cursor:pointer;">Print / Save PDF</button>
            <button onclick="window.close()" style="padding:8px 16px; background:#f5f5f5; color:#121212; border:1px solid #ddd; border-radius:4px; font-size:12px; font-weight:500; cursor:pointer; margin-left:8px;">Close</button>
        </div>

        <div class="header">
            <h1>Costikyan Custom Carpet</h1>
            <p>Trade Portal — Quote Specification</p>
        </div>

        <div class="meta-grid">
            <div class="meta-block">
                <h3>Quote Number</h3>
                <p style="font-family:'Lusitana',serif; font-size:18px; font-weight:700;">{{ $quote->quote_number }}</p>
            </div>
            <div class="meta-block">
                <h3>Status</h3>
                <p>
                    <span class="status-badge" style="{{
                        match($quote->status) {
                            'draft' => 'color:#57534e; background:#f5f5f4; border:1px solid #d6d3d1;',
                            'sent' => 'color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe;',
                            'approved' => 'color:#15803d; background:#dcfce7; border:1px solid #bbf7d0;',
                            'expired' => 'color:#c2410c; background:#ffedd5; border:1px solid #fed7aa;',
                            default => 'color:#57534e; background:#f5f5f4; border:1px solid #d6d3d1;',
                        }
                    }}">{{ ucfirst($quote->status) }}</span>
                </p>
            </div>
            <div class="meta-block">
                <h3>Project</h3>
                <p>{{ $quote->project?->name ?? '—' }}</p>
            </div>
            <div class="meta-block">
                <h3>Date</h3>
                <p>{{ $quote->created_at->format('F j, Y') }}</p>
            </div>
            <div class="meta-block">
                <h3>Trade Partner</h3>
                <p>{{ Auth::user()->name }} — {{ Auth::user()->company_name ?? 'Trade Partner' }}</p>
            </div>
            <div class="meta-block">
                <h3>Discount</h3>
                <p>{{ Auth::user()->trade_discount ?? 0 }}% off MSRP</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align:center;">Items</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Rug order per quote specification</td>
                    <td style="text-align:center;">{{ $quote->items_count }}</td>
                    <td style="text-align:right; font-family:'Lusitana',serif; font-weight:700;">${{ number_format($quote->total, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2">Quote Total</td>
                    <td style="text-align:right; font-family:'Lusitana',serif; font-weight:700;">${{ number_format($quote->total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="background:#fafafa; border-radius:4px; padding:16px; margin-bottom:24px;">
            <p style="font-size:11px; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:rgba(18,18,18,0.45); margin-bottom:8px;">Notes</p>
            <p style="font-size:12px; color:rgba(18,18,18,0.7); line-height:1.6;">This quote is valid for 30 days from the date issued. Pricing reflects trade discount eligibility. Custom sizes and finishes may affect final pricing. Please contact your account manager to proceed.</p>
        </div>

        <div class="footer">
            <p>Costikyan Custom Carpet — Est. 1886</p>
            <p style="margin-top:4px;">For questions, contact your trade account manager.</p>
        </div>
    </div>
</body>
</html>

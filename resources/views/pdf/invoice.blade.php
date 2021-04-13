<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #1a1f36; background: #fff; }

        .invoice-wrapper { padding: 40px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; border-bottom: 3px solid #667eea; padding-bottom: 20px; }
        .company-name { font-size: 22px; font-weight: 700; color: #667eea; }
        .invoice-title { font-size: 32px; font-weight: 700; color: #667eea; text-align: right; }
        .invoice-number { font-size: 14px; color: #6b7280; text-align: right; }

        /* Info Section */
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-block { width: 48%; }
        .info-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin-bottom: 4px; }
        .info-value { font-weight: 600; color: #1a1f36; }
        .info-value-sm { color: #6b7280; font-size: 12px; }

        /* Status Badge */
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
        .status-draft    { background: #e2e8f0; color: #475569; }
        .status-sent     { background: #dbeafe; color: #1d4ed8; }
        .status-paid     { background: #dcfce7; color: #16a34a; }
        .status-overdue  { background: #fee2e2; color: #dc2626; }

        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .items-table thead th { background: #1a1f36; color: #fff; padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .items-table thead th.text-right { text-align: right; }
        .items-table tbody tr:nth-child(even) { background: #f8fafc; }
        .items-table tbody td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; vertical-align: top; }
        .items-table tbody td.text-right { text-align: right; }

        /* Totals */
        .totals-section { display: flex; justify-content: flex-end; margin-bottom: 30px; }
        .totals-table { width: 300px; }
        .totals-table tr td { padding: 5px 8px; }
        .totals-table tr td:last-child { text-align: right; font-weight: 600; }
        .totals-table .total-row-label { color: #6b7280; }
        .grand-total-row { background: #1a1f36; color: #fff; }
        .grand-total-row td { padding: 10px 8px; font-size: 15px; font-weight: 700; }

        /* Payments */
        .payments-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .payments-table thead th { background: #f0fdf4; color: #166534; padding: 8px 10px; font-size: 11px; font-weight: 600; text-transform: uppercase; border-bottom: 2px solid #bbf7d0; }
        .payments-table tbody td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; font-size: 12px; }
        .section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #667eea; margin-bottom: 10px; border-bottom: 2px solid #667eea; padding-bottom: 4px; }

        /* Footer */
        .footer { margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 16px; text-align: center; color: #9ca3af; font-size: 11px; }
        .notes-box { background: #fffbeb; border-left: 3px solid #f59e0b; padding: 12px 16px; border-radius: 4px; margin-bottom: 24px; font-size: 12px; }
    </style>
</head>
<body>
<div class="invoice-wrapper">

    <!-- Header -->
    <div class="header">
        <div>
            <div class="company-name">InvoicePro</div>
            <div style="color:#6b7280; font-size:12px; margin-top:4px;">Invoice Management System</div>
        </div>
        <div>
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            <div style="margin-top:8px;">
                @if($invoice->is_overdue && $invoice->status !== 'paid')
                    <span class="status-badge status-overdue">Overdue</span>
                @elseif($invoice->status === 'paid')
                    <span class="status-badge status-paid">Paid</span>
                @elseif($invoice->status === 'sent')
                    <span class="status-badge status-sent">Sent</span>
                @else
                    <span class="status-badge status-draft">Draft</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Bill To & Dates -->
    <div class="info-section">
        <div class="info-block">
            <div class="info-label">Bill To</div>
            <div class="info-value">{{ $invoice->user->name }}</div>
            @if($invoice->user->company)
                <div class="info-value-sm">{{ $invoice->user->company }}</div>
            @endif
            @if($invoice->user->email)
                <div class="info-value-sm">{{ $invoice->user->email }}</div>
            @endif
            @if($invoice->user->phone)
                <div class="info-value-sm">{{ $invoice->user->phone }}</div>
            @endif
            @if($invoice->user->address)
                <div class="info-value-sm" style="margin-top:4px;">{{ $invoice->user->address }}</div>
            @endif
        </div>
        <div class="info-block" style="text-align:right;">
            <table style="width:100%; text-align:right;">
                <tr>
                    <td class="info-label">Invoice Date</td>
                    <td class="info-value">{{ $invoice->invoice_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Due Date</td>
                    <td class="info-value" style="{{ $invoice->is_overdue && $invoice->status !== 'paid' ? 'color:#dc2626;' : '' }}">
                        {{ $invoice->due_date->format('d M Y') }}
                    </td>
                </tr>
                @if($invoice->payment_date)
                <tr>
                    <td class="info-label">Payment Date</td>
                    <td class="info-value">{{ $invoice->payment_date->format('d M Y') }}</td>
                </tr>
                @endif
                @if($invoice->payment_type)
                <tr>
                    <td class="info-label">Payment Type</td>
                    <td class="info-value">{{ ucwords(str_replace('_', ' ', $invoice->payment_type)) }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Line Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:45%;">Description</th>
                <th style="width:15%;" class="text-right">Qty</th>
                <th style="width:17%;" class="text-right">Unit Price (₹)</th>
                <th style="width:18%;" class="text-right">Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td class="total-row-label">Subtotal</td>
                <td>₹{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
            <tr>
                <td class="total-row-label">Discount ({{ $invoice->discount_percent }}%)</td>
                <td style="color:#dc2626;">-₹{{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="total-row-label">GST ({{ $invoice->gst_percent }}%)</td>
                <td>+₹{{ number_format($invoice->gst_amount, 2) }}</td>
            </tr>
            <tr class="grand-total-row">
                <td>GRAND TOTAL</td>
                <td>₹{{ number_format($invoice->grand_total, 2) }}</td>
            </tr>
            @php $totalPaid = $invoice->payments->sum('amount_paid'); $balance = $invoice->grand_total - $totalPaid; @endphp
            @if($totalPaid > 0)
            <tr>
                <td class="total-row-label" style="color:#16a34a;">Amount Paid</td>
                <td style="color:#16a34a;">-₹{{ number_format($totalPaid, 2) }}</td>
            </tr>
            @if($balance > 0.01)
            <tr style="font-weight:700; color:#dc2626;">
                <td>Balance Due</td>
                <td>₹{{ number_format($balance, 2) }}</td>
            </tr>
            @endif
            @endif
        </table>
    </div>

    @if($invoice->notes)
    <!-- Notes -->
    <div class="notes-box">
        <strong>Notes:</strong> {{ $invoice->notes }}
    </div>
    @endif

    <!-- Payment History -->
    @if($invoice->payments->count() > 0)
    <div class="section-title">Payment History</div>
    <table class="payments-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->payments as $payment)
            <tr>
                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                <td style="color:#16a34a; font-weight:600;">₹{{ number_format($payment->amount_paid, 2) }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $payment->payment_type)) }}</td>
                <td>{{ $payment->reference_number ?? '-' }}</td>
                <td>{{ $payment->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p style="margin-top:4px;">Generated by InvoicePro &mdash; {{ now()->format('d M Y, H:i') }}</p>
    </div>

</div>
</body>
</html>

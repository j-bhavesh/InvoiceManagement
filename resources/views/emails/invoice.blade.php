<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #fff; }
        .header { background: linear-gradient(135deg, #1a1f36, #2d3561); padding: 32px 40px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.7); margin: 4px 0 0; font-size: 13px; }
        .content { padding: 32px 40px; }
        .greeting { font-size: 16px; color: #1a1f36; margin-bottom: 16px; }
        .message { color: #6b7280; line-height: 1.6; margin-bottom: 24px; }
        .invoice-box { background: #f8fafc; border-radius: 8px; padding: 20px; margin-bottom: 24px; border: 1px solid #e5e7eb; }
        .invoice-box table { width: 100%; border-collapse: collapse; }
        .invoice-box td { padding: 6px 0; }
        .invoice-box td:first-child { color: #9ca3af; font-size: 13px; }
        .invoice-box td:last-child { font-weight: 600; color: #1a1f36; text-align: right; }
        .amount-row td { font-size: 18px; color: #667eea; font-weight: 700; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .status-paid { background: #dcfce7; color: #16a34a; }
        .status-sent { background: #dbeafe; color: #1d4ed8; }
        .status-draft { background: #e2e8f0; color: #475569; }
        .status-overdue { background: #fee2e2; color: #dc2626; }
        .cta-section { text-align: center; margin: 24px 0; }
        .cta-note { color: #9ca3af; font-size: 12px; margin-top: 8px; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; }
        .footer a { color: #667eea; text-decoration: none; }
        .overdue-alert { background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; color: #dc2626; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Header -->
    <div class="header">
        <h1>InvoicePro</h1>
        <p>Invoice Management System</p>
    </div>

    <!-- Content -->
    <div class="content">
        @if($invoice->is_overdue && $invoice->status !== 'paid')
        <div class="overdue-alert">
            ⚠️ <strong>This invoice is overdue.</strong> Please arrange payment at the earliest.
        </div>
        @endif

        <div class="greeting">
            Dear {{ $invoice->user->name }},
        </div>

        <p class="message">
            Please find attached the invoice
            <strong>{{ $invoice->invoice_number }}</strong>
            @if($invoice->status === 'paid')
                which has been <strong>paid in full</strong>. This is your payment confirmation.
            @else
                for your reference. Please review and arrange payment before the due date.
            @endif
        </p>

        <!-- Invoice Summary -->
        <div class="invoice-box">
            <table>
                <tr>
                    <td>Invoice Number</td>
                    <td><strong>{{ $invoice->invoice_number }}</strong></td>
                </tr>
                <tr>
                    <td>Invoice Date</td>
                    <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td>Due Date</td>
                    <td style="{{ $invoice->is_overdue && $invoice->status !== 'paid' ? 'color:#dc2626;' : '' }}">
                        {{ $invoice->due_date->format('d M Y') }}
                    </td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>
                        @if($invoice->is_overdue && $invoice->status !== 'paid')
                            <span class="status-badge status-overdue">Overdue</span>
                        @elseif($invoice->status === 'paid')
                            <span class="status-badge status-paid">Paid</span>
                        @elseif($invoice->status === 'sent')
                            <span class="status-badge status-sent">Sent</span>
                        @else
                            <span class="status-badge status-draft">Draft</span>
                        @endif
                    </td>
                </tr>
                <tr class="amount-row">
                    <td>Grand Total</td>
                    <td>₹{{ number_format($invoice->grand_total, 2) }}</td>
                </tr>
                @php $totalPaid = $invoice->payments->sum('amount_paid'); $balance = $invoice->grand_total - $totalPaid; @endphp
                @if($totalPaid > 0)
                <tr>
                    <td>Amount Paid</td>
                    <td style="color:#16a34a;">₹{{ number_format($totalPaid, 2) }}</td>
                </tr>
                @if($balance > 0.01)
                <tr>
                    <td>Balance Due</td>
                    <td style="color:#dc2626; font-weight:700;">₹{{ number_format($balance, 2) }}</td>
                </tr>
                @endif
                @endif
            </table>
        </div>

        @if($invoice->payment_type)
        <p class="message" style="margin-bottom:0;">
            <strong>Payment Method:</strong> {{ ucwords(str_replace('_', ' ', $invoice->payment_type)) }}
        </p>
        @endif

        @if($invoice->notes)
        <hr class="divider">
        <p class="message" style="margin-bottom:0;">
            <strong>Notes:</strong> {{ $invoice->notes }}
        </p>
        @endif

        <hr class="divider">

        <div class="cta-section">
            <p class="message">
                The full invoice PDF is attached to this email for your records.
            </p>
            <p class="cta-note">
                For any queries, please contact us at
                <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
            </p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This email was sent by <strong>InvoicePro</strong></p>
        <p style="margin-top:4px;">&copy; {{ date('Y') }} InvoicePro. All rights reserved.</p>
    </div>
</div>
</body>
</html>

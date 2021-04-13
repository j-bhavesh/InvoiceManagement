@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page-title', 'Invoice ' . $invoice->invoice_number)

@section('styles')
<style>
    .info-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; }
    .info-value { font-weight: 500; color: #1a1f36; margin-top: 2px; }
    .payment-type-badge { background: #eff6ff; color: #1d4ed8; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Invoice Main -->
    <div class="col-lg-8">
        <div class="card mb-3">
            <!-- Invoice Header -->
            <div class="card-body border-bottom">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="font-weight-bold mb-1">{{ $invoice->invoice_number }}</h4>
                        <div>
                            @if($invoice->is_overdue && $invoice->status !== 'paid')
                                <span class="badge badge-overdue px-3 py-1 rounded-pill">🔴 Overdue</span>
                            @elseif($invoice->status === 'paid')
                                <span class="badge badge-paid px-3 py-1 rounded-pill">✅ Paid</span>
                            @elseif($invoice->status === 'sent')
                                <span class="badge badge-sent px-3 py-1 rounded-pill">📤 Sent</span>
                            @else
                                <span class="badge badge-draft px-3 py-1 rounded-pill">📝 Draft</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="h3 font-weight-bold text-primary mb-0">₹{{ number_format($invoice->grand_total, 2) }}</div>
                        @php $balance = $invoice->grand_total - $invoice->payments->sum('amount_paid'); @endphp
                        @if($balance > 0.01 && $invoice->status !== 'paid')
                            <small class="text-danger">Balance Due: ₹{{ number_format($balance, 2) }}</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Client & Dates -->
            <div class="card-body border-bottom">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">Billed To</div>
                        <div class="info-value">{{ $invoice->user->name }}</div>
                        @if($invoice->user->company)<div class="text-muted small">{{ $invoice->user->company }}</div>@endif
                        @if($invoice->user->email)<div class="text-muted small">{{ $invoice->user->email }}</div>@endif
                        @if($invoice->user->phone)<div class="text-muted small">{{ $invoice->user->phone }}</div>@endif
                        @if($invoice->user->address)<div class="text-muted small">{{ $invoice->user->address }}</div>@endif
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Invoice Date</div>
                        <div class="info-value">{{ $invoice->invoice_date->format('d M Y') }}</div>
                        <div class="info-label mt-3">Due Date</div>
                        <div class="info-value {{ $invoice->is_overdue && $invoice->status !== 'paid' ? 'text-danger' : '' }}">
                            {{ $invoice->due_date->format('d M Y') }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        @if($invoice->payment_date)
                        <div class="info-label">Payment Date</div>
                        <div class="info-value">{{ $invoice->payment_date->format('d M Y') }}</div>
                        @endif
                        @if($invoice->payment_type)
                        <div class="info-label mt-3">Payment Type</div>
                        <div class="info-value">
                            <span class="payment-type-badge">{{ ucwords(str_replace('_', ' ', $invoice->payment_type)) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Line Items -->
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"></td>
                            <td class="text-right text-muted">Subtotal</td>
                            <td class="text-right">₹{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->discount_amount > 0)
                        <tr>
                            <td colspan="3"></td>
                            <td class="text-right text-muted">Discount ({{ $invoice->discount_percent }}%)</td>
                            <td class="text-right text-danger">-₹{{ number_format($invoice->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3"></td>
                            <td class="text-right text-muted">GST ({{ $invoice->gst_percent }}%)</td>
                            <td class="text-right">+₹{{ number_format($invoice->gst_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td class="text-right font-weight-bold" style="font-size:1.1rem;">Grand Total</td>
                            <td class="text-right font-weight-bold text-primary" style="font-size:1.1rem;">₹{{ number_format($invoice->grand_total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($invoice->notes)
            <div class="card-body border-top">
                <div class="info-label">Notes</div>
                <div class="text-muted mt-1" style="white-space:pre-line;">{{ $invoice->notes }}</div>
            </div>
            @endif
        </div>

        <!-- Payments Table -->
        @if($invoice->payments->count() > 0)
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-money-bill-wave mr-2 text-success"></i>Payment History</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Notes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                            <td class="text-success font-weight-bold">₹{{ number_format($payment->amount_paid, 2) }}</td>
                            <td>
                                <span class="payment-type-badge">{{ ucwords(str_replace('_', ' ', $payment->payment_type)) }}</span>
                            </td>
                            <td>{{ $payment->reference_number ?? '-' }}</td>
                            <td>{{ $payment->notes ?? '-' }}</td>
                            <td>
                                <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this payment record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>Total Paid</strong></td>
                            <td class="text-success font-weight-bold">₹{{ number_format($invoice->payments->sum('amount_paid'), 2) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Actions Sidebar -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-cogs mr-2 text-primary"></i>Actions</div>
            <div class="card-body">
                <div class="d-flex flex-column gap-2">
                    <!-- Record Payment -->
                    @if($invoice->status !== 'paid')
                    <a href="{{ route('payments.create', $invoice) }}" class="btn btn-success btn-block">
                        <i class="fas fa-money-bill-wave mr-2"></i>Record Payment
                    </a>
                    @endif

                    <!-- Mark as Sent -->
                    @if($invoice->status === 'draft')
                    <form action="{{ route('invoices.send', $invoice) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-info btn-block text-white">
                            <i class="fas fa-paper-plane mr-2"></i>Mark as Sent
                        </button>
                    </form>
                    @endif

                    <!-- Download PDF -->
                    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-file-pdf mr-2 text-danger"></i>Download PDF
                    </a>

                    <!-- Send Email -->
                    <form action="{{ route('invoices.email', $invoice) }}" method="POST"
                        onsubmit="return confirm('Send invoice to {{ $invoice->user->email }}?')">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-envelope mr-2"></i>Send via Email
                        </button>
                    </form>

                    <hr>

                    <!-- Edit -->
                    @if($invoice->status !== 'paid')
                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-warning btn-block">
                        <i class="fas fa-edit mr-2"></i>Edit Invoice
                    </a>
                    @endif

                    <!-- Delete -->
                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST"
                        onsubmit="return confirm('Delete invoice {{ $invoice->invoice_number }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-block">
                            <i class="fas fa-trash mr-2"></i>Delete Invoice
                        </button>
                    </form>

                    <a href="{{ route('invoices.index') }}" class="btn btn-link btn-block text-muted">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Invoices
                    </a>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="card">
            <div class="card-header"><i class="fas fa-chart-pie mr-2 text-primary"></i>Payment Summary</div>
            <div class="card-body">
                @php
                    $totalPaid = $invoice->payments->sum('amount_paid');
                    $balance   = max(0, $invoice->grand_total - $totalPaid);
                    $paidPct   = $invoice->grand_total > 0 ? min(100, ($totalPaid / $invoice->grand_total) * 100) : 0;
                @endphp
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Progress</small>
                    <small class="font-weight-bold">{{ round($paidPct) }}%</small>
                </div>
                <div class="progress mb-3" style="height:8px;">
                    <div class="progress-bar bg-success" style="width:{{ $paidPct }}%"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">Grand Total</div>
                        <div class="font-weight-bold">₹{{ number_format($invoice->grand_total, 2) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-muted small">Paid</div>
                        <div class="font-weight-bold text-success">₹{{ number_format($totalPaid, 2) }}</div>
                    </div>
                </div>
                @if($balance > 0.01)
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <div class="font-weight-bold">Balance Due</div>
                    <div class="font-weight-bold text-danger">₹{{ number_format($balance, 2) }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<!-- Stat Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card stat-blue">
            <p>Total Invoices</p>
            <h3>{{ $totalInvoices }}</h3>
            <i class="fas fa-file-invoice stat-icon"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card stat-green">
            <p>Paid Invoices</p>
            <h3>{{ $paidInvoices }}</h3>
            <i class="fas fa-check-circle stat-icon"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card stat-orange">
            <p>Pending Amount</p>
            <h3>₹{{ number_format($pendingAmount, 0) }}</h3>
            <i class="fas fa-clock stat-icon"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card stat-red">
            <p>Overdue Invoices</p>
            <h3>{{ $overdueCount }} @if($overdueCount > 0)<i class="fas fa-exclamation-triangle" style="font-size:1.2rem;"></i>@endif</h3>
            <i class="fas fa-exclamation-circle stat-icon"></i>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Invoices -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list mr-2 text-primary"></i>Recent Invoices</span>
                <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> New Invoice
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="font-weight-bold text-primary">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $invoice->user->name }}</td>
                                <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                <td>₹{{ number_format($invoice->grand_total, 2) }}</td>
                                <td>
                                    @if($invoice->is_overdue && $invoice->status !== 'paid')
                                        <span class="badge badge-overdue px-2 py-1 rounded">Overdue</span>
                                    @elseif($invoice->status === 'paid')
                                        <span class="badge badge-paid px-2 py-1 rounded">Paid</span>
                                    @elseif($invoice->status === 'sent')
                                        <span class="badge badge-sent px-2 py-1 rounded">Sent</span>
                                    @else
                                        <span class="badge badge-draft px-2 py-1 rounded">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-file-invoice fa-2x mb-2 d-block"></i>
                                    No invoices yet. <a href="{{ route('invoices.create') }}">Create your first one</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Alerts -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle mr-2 text-danger"></i>Overdue Alerts
            </div>
            <div class="card-body p-0">
                @forelse($overdueInvoices as $invoice)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="mr-3">
                        <div style="width:40px;height:40px;background:#fee2e2;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-exclamation text-danger"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="font-weight-bold text-sm" style="font-size:0.85rem;">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-dark">{{ $invoice->invoice_number }}</a>
                        </div>
                        <div class="text-muted" style="font-size:0.8rem;">{{ $invoice->user->name }}</div>
                        <div class="text-danger" style="font-size:0.75rem;">
                            Due: {{ $invoice->due_date->format('d M Y') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-weight-bold text-danger" style="font-size:0.85rem;">
                            ₹{{ number_format($invoice->grand_total, 0) }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                    <small>No overdue invoices!</small>
                </div>
                @endforelse
            </div>
            @if($overdueCount > 5)
            <div class="card-footer text-center">
                <a href="{{ route('invoices.index', ['status' => 'overdue']) }}" class="btn btn-sm btn-outline-danger">
                    View all {{ $overdueCount }} overdue
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

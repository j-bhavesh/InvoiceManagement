@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('invoices.index') }}" class="d-flex flex-wrap gap-2 align-items-end">
            <div class="form-group mb-0 mr-2">
                <label class="small font-weight-bold">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Invoice # or client..." value="{{ request('search') }}">
            </div>
            <div class="form-group mb-0 mr-2">
                <label class="small font-weight-bold">Status</label>
                <select name="status" class="form-control form-control-sm">
                    <option value="">All</option>
                    <option value="draft"  {{ request('status') === 'draft'  ? 'selected' : '' }}>Draft</option>
                    <option value="sent"   {{ request('status') === 'sent'   ? 'selected' : '' }}>Sent</option>
                    <option value="paid"   {{ request('status') === 'paid'   ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="form-group mb-0 mr-2">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-search mr-1"></i>Filter
                </button>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary ml-1">Clear</a>
            </div>
            <div class="ml-auto">
                <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i>New Invoice
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Client</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    @php
                        $totalPaid = $invoice->payments->sum('amount_paid');
                        $balance   = $invoice->grand_total - $totalPaid;
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('invoices.show', $invoice) }}" class="font-weight-bold text-primary">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('users.show', $invoice->user) }}" class="text-dark">
                                {{ $invoice->user->name }}
                            </a>
                            @if($invoice->user->company)
                                <small class="d-block text-muted">{{ $invoice->user->company }}</small>
                            @endif
                        </td>
                        <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                        <td>
                            {{ $invoice->due_date->format('d M Y') }}
                            @if($invoice->is_overdue && $invoice->status !== 'paid')
                                <i class="fas fa-exclamation-circle text-danger ml-1" title="Overdue"></i>
                            @endif
                        </td>
                        <td>₹{{ number_format($invoice->grand_total, 2) }}</td>
                        <td>
                            @if($totalPaid > 0)
                                <span class="text-success">₹{{ number_format($totalPaid, 2) }}</span>
                                @if($balance > 0.01)
                                    <small class="d-block text-danger">Due: ₹{{ number_format($balance, 2) }}</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
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
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($invoice->status !== 'paid')
                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-info" title="Download PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this invoice?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice fa-3x mb-3 d-block"></i>
                            No invoices found.
                            <a href="{{ route('invoices.create') }}">Create your first invoice</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($invoices->hasPages())
    <div class="card-footer">
        {{ $invoices->links() }}
    </div>
    @endif
</div>
@endsection

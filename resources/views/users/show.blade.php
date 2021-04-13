@extends('layouts.app')

@section('title', 'Client: ' . $user->name)
@section('page-title', 'Client Profile')

@section('content')
<div class="row">
    <!-- Profile Card -->
    <div class="col-lg-4 mb-4">
        <div class="card text-center">
            <div class="card-body py-4">
                <div style="width:80px;height:80px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:2rem;margin:0 auto 16px;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="font-weight-bold">{{ $user->name }}</h5>
                <p class="text-muted mb-1">{{ $user->email }}</p>
                @if($user->company)
                    <p class="text-muted mb-1"><i class="fas fa-building mr-1"></i>{{ $user->company }}</p>
                @endif
                @if($user->phone)
                    <p class="text-muted mb-1"><i class="fas fa-phone mr-1"></i>{{ $user->phone }}</p>
                @endif
                @if($user->address)
                    <p class="text-muted small"><i class="fas fa-map-marker-alt mr-1"></i>{{ $user->address }}</p>
                @endif
                <div class="mt-3">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary mr-2">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <a href="{{ route('invoices.create') }}?user_id={{ $user->id }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i>New Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-invoice mr-2 text-primary"></i>Invoices ({{ $invoices->total() }})
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="font-weight-bold text-primary">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                <td>{{ $invoice->due_date->format('d M Y') }}</td>
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
                                <td colspan="6" class="text-center py-3 text-muted">No invoices yet.</td>
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
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <!-- Invoice Summary -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center border-right">
                        <div class="text-muted small">Invoice</div>
                        <div class="font-weight-bold text-primary">{{ $invoice->invoice_number }}</div>
                        <div class="text-muted small">{{ $invoice->user->name }}</div>
                    </div>
                    <div class="col-md-4 text-center border-right">
                        <div class="text-muted small">Grand Total</div>
                        <div class="font-weight-bold">₹{{ number_format($invoice->grand_total, 2) }}</div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="text-muted small">Balance Due</div>
                        @php $balance = $invoice->grand_total - $invoice->payments->sum('amount_paid'); @endphp
                        <div class="font-weight-bold text-danger">₹{{ number_format($balance, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-money-bill-wave mr-2 text-success"></i>Payment Details
            </div>
            <div class="card-body">
                <form action="{{ route('payments.store', $invoice) }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Amount Paid (₹) <span class="text-danger">*</span></label>
                                <input type="number" name="amount_paid" class="form-control @error('amount_paid') is-invalid @enderror"
                                    value="{{ old('amount_paid', number_format($balance, 2, '.', '')) }}"
                                    min="0.01" step="0.01" required>
                                @error('amount_paid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror"
                                    value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Payment Type <span class="text-danger">*</span></label>
                                <select name="payment_type" class="form-control @error('payment_type') is-invalid @enderror" required>
                                    <option value="">— Select Type —</option>
                                    @foreach(['cash' => 'Cash', 'cheque' => 'Cheque', 'bank_transfer' => 'Bank Transfer', 'upi' => 'UPI', 'card' => 'Card'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('payment_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('payment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Reference Number</label>
                                <input type="text" name="reference_number" class="form-control"
                                    value="{{ old('reference_number') }}" placeholder="Cheque no. / UTR / Transaction ID">
                                <small class="form-text text-muted">Cheque no., UTR, Transaction ID etc.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Notes</label>
                                <textarea name="notes" rows="2" class="form-control" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-1"></i>Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

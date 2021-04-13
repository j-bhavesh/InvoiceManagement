@extends('layouts.app')

@section('title', 'Create Invoice')
@section('page-title', 'Create New Invoice')

@section('styles')
<style>
    .item-row { background: #fff; border-radius: 8px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb; }
    .item-row:hover { border-color: #667eea; }
    .totals-box { background: #f8fafc; border-radius: 8px; padding: 20px; }
    .totals-box .total-row { display: flex; justify-content: space-between; padding: 4px 0; }
    .totals-box .grand-total { font-size: 1.2rem; font-weight: 700; color: #1a1f36; border-top: 2px solid #e5e7eb; padding-top: 8px; margin-top: 4px; }
</style>
@endsection

@section('content')
<form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
    @csrf
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Invoice Details -->
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-info-circle mr-2 text-primary"></i>Invoice Details</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Invoice Number</label>
                                <input type="text" class="form-control" value="{{ $invoiceNumber }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror"
                                    value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                                    value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Client <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                    <option value="">— Select Client —</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', request('user_id')) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} {{ $user->company ? '(' . $user->company . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Items -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list mr-2 text-primary"></i>Line Items</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-item">
                        <i class="fas fa-plus mr-1"></i>Add Item
                    </button>
                </div>
                <div class="card-body">
                    <div class="row text-muted small font-weight-bold mb-2 px-2">
                        <div class="col-5">Description</div>
                        <div class="col-2">Qty</div>
                        <div class="col-2">Unit Price (₹)</div>
                        <div class="col-2">Total (₹)</div>
                        <div class="col-1"></div>
                    </div>
                    <div id="items-container">
                        @if(old('items'))
                            @foreach(old('items') as $i => $item)
                            <div class="item-row row align-items-center mx-0">
                                <div class="col-5 px-1">
                                    <input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm"
                                        value="{{ $item['description'] }}" placeholder="Service or product description" required>
                                </div>
                                <div class="col-2 px-1">
                                    <input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm item-qty"
                                        value="{{ $item['quantity'] }}" min="0.01" step="0.01" required>
                                </div>
                                <div class="col-2 px-1">
                                    <input type="number" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm item-price"
                                        value="{{ $item['unit_price'] }}" min="0" step="0.01" required>
                                </div>
                                <div class="col-2 px-1">
                                    <input type="text" class="form-control form-control-sm item-total" readonly
                                        value="{{ number_format($item['quantity'] * $item['unit_price'], 2) }}">
                                </div>
                                <div class="col-1 px-1 text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div class="item-row row align-items-center mx-0">
                            <div class="col-5 px-1">
                                <input type="text" name="items[0][description]" class="form-control form-control-sm"
                                    placeholder="Service or product description" required>
                            </div>
                            <div class="col-2 px-1">
                                <input type="number" name="items[0][quantity]" class="form-control form-control-sm item-qty"
                                    value="1" min="0.01" step="0.01" required>
                            </div>
                            <div class="col-2 px-1">
                                <input type="number" name="items[0][unit_price]" class="form-control form-control-sm item-price"
                                    value="0" min="0" step="0.01" required>
                            </div>
                            <div class="col-2 px-1">
                                <input type="text" class="form-control form-control-sm item-total" readonly value="0.00">
                            </div>
                            <div class="col-1 px-1 text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-sticky-note mr-2 text-primary"></i>Notes</div>
                <div class="card-body">
                    <textarea name="notes" rows="3" class="form-control" placeholder="Payment terms, thank you note, etc.">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Tax & Discount -->
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-percent mr-2 text-primary"></i>Tax & Discount</div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Discount (%)</label>
                        <input type="number" name="discount_percent" id="discount_percent" class="form-control"
                            value="{{ old('discount_percent', 0) }}" min="0" max="100" step="0.01">
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">GST (%)</label>
                        <input type="number" name="gst_percent" id="gst_percent" class="form-control"
                            value="{{ old('gst_percent', 18) }}" min="0" max="100" step="0.01" required>
                    </div>
                </div>
            </div>

            <!-- Totals -->
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-calculator mr-2 text-primary"></i>Summary</div>
                <div class="card-body p-3">
                    <div class="totals-box">
                        <div class="total-row"><span class="text-muted">Subtotal</span> <span id="display-subtotal">₹0.00</span></div>
                        <div class="total-row"><span class="text-muted">Discount</span> <span id="display-discount" class="text-danger">-₹0.00</span></div>
                        <div class="total-row"><span class="text-muted">GST</span> <span id="display-gst">+₹0.00</span></div>
                        <div class="total-row grand-total"><span>Grand Total</span> <span id="display-grand-total">₹0.00</span></div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-2"></i>Create Invoice
                    </button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-block mt-2">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
let itemIndex = {{ old('items') ? count(old('items')) : 1 }};

function recalculate() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.item-qty').value)   || 0;
        const price = parseFloat(row.querySelector('.item-price').value)  || 0;
        const total = qty * price;
        row.querySelector('.item-total').value = total.toFixed(2);
        subtotal += total;
    });

    const discountPct  = parseFloat(document.getElementById('discount_percent').value) || 0;
    const gstPct       = parseFloat(document.getElementById('gst_percent').value)       || 0;
    const discountAmt  = subtotal * discountPct / 100;
    const afterDisc    = subtotal - discountAmt;
    const gstAmt       = afterDisc * gstPct / 100;
    const grandTotal   = afterDisc + gstAmt;

    document.getElementById('display-subtotal').textContent   = '₹' + subtotal.toFixed(2);
    document.getElementById('display-discount').textContent   = '-₹' + discountAmt.toFixed(2);
    document.getElementById('display-gst').textContent        = '+₹' + gstAmt.toFixed(2);
    document.getElementById('display-grand-total').textContent = '₹' + grandTotal.toFixed(2);
}

document.addEventListener('input', function(e) {
    if (e.target.matches('.item-qty, .item-price, #discount_percent, #gst_percent')) {
        recalculate();
    }
});

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const div = document.createElement('div');
    div.className = 'item-row row align-items-center mx-0';
    div.innerHTML = `
        <div class="col-5 px-1">
            <input type="text" name="items[${itemIndex}][description]" class="form-control form-control-sm" placeholder="Service or product description" required>
        </div>
        <div class="col-2 px-1">
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm item-qty" value="1" min="0.01" step="0.01" required>
        </div>
        <div class="col-2 px-1">
            <input type="number" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm item-price" value="0" min="0" step="0.01" required>
        </div>
        <div class="col-2 px-1">
            <input type="text" class="form-control form-control-sm item-total" readonly value="0.00">
        </div>
        <div class="col-1 px-1 text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button>
        </div>
    `;
    container.appendChild(div);
    itemIndex++;
});

document.getElementById('items-container').addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            e.target.closest('.item-row').remove();
            recalculate();
        }
    }
});

recalculate();
</script>
@endsection

@extends('layouts.app')

@section('title', 'Edit Invoice ' . $invoice->invoice_number)
@section('page-title', 'Edit Invoice')

@section('styles')
<style>
    .item-row { background: #fff; border-radius: 8px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb; }
    .totals-box { background: #f8fafc; border-radius: 8px; padding: 20px; }
    .totals-box .total-row { display: flex; justify-content: space-between; padding: 4px 0; }
    .totals-box .grand-total { font-size: 1.2rem; font-weight: 700; color: #1a1f36; border-top: 2px solid #e5e7eb; padding-top: 8px; margin-top: 4px; }
</style>
@endsection

@section('content')
<form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoice-form">
    @csrf @method('PUT')
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-info-circle mr-2 text-primary"></i>Invoice Details</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Invoice Number</label>
                                <input type="text" class="form-control" value="{{ $invoice->invoice_number }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" class="form-control"
                                    value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" class="form-control"
                                    value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Client <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-control" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $invoice->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} {{ $user->company ? '(' . $user->company . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                        @foreach($invoice->items as $i => $item)
                        <div class="item-row row align-items-center mx-0">
                            <div class="col-5 px-1">
                                <input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm"
                                    value="{{ old("items.{$i}.description", $item->description) }}" required>
                            </div>
                            <div class="col-2 px-1">
                                <input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm item-qty"
                                    value="{{ old("items.{$i}.quantity", $item->quantity) }}" min="0.01" step="0.01" required>
                            </div>
                            <div class="col-2 px-1">
                                <input type="number" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm item-price"
                                    value="{{ old("items.{$i}.unit_price", $item->unit_price) }}" min="0" step="0.01" required>
                            </div>
                            <div class="col-2 px-1">
                                <input type="text" class="form-control form-control-sm item-total" readonly
                                    value="{{ number_format($item->quantity * $item->unit_price, 2) }}">
                            </div>
                            <div class="col-1 px-1 text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-sticky-note mr-2 text-primary"></i>Notes</div>
                <div class="card-body">
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes', $invoice->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-percent mr-2 text-primary"></i>Tax & Discount</div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Discount (%)</label>
                        <input type="number" name="discount_percent" id="discount_percent" class="form-control"
                            value="{{ old('discount_percent', $invoice->discount_percent) }}" min="0" max="100" step="0.01">
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">GST (%)</label>
                        <input type="number" name="gst_percent" id="gst_percent" class="form-control"
                            value="{{ old('gst_percent', $invoice->gst_percent) }}" min="0" max="100" step="0.01" required>
                    </div>
                </div>
            </div>

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

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-2"></i>Update Invoice
                    </button>
                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary btn-block mt-2">
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
let itemIndex = {{ $invoice->items->count() }};

function recalculate() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.item-qty').value)  || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const total = qty * price;
        row.querySelector('.item-total').value = total.toFixed(2);
        subtotal += total;
    });
    const discountPct = parseFloat(document.getElementById('discount_percent').value) || 0;
    const gstPct      = parseFloat(document.getElementById('gst_percent').value)      || 0;
    const discountAmt = subtotal * discountPct / 100;
    const afterDisc   = subtotal - discountAmt;
    const gstAmt      = afterDisc * gstPct / 100;
    const grandTotal  = afterDisc + gstAmt;
    document.getElementById('display-subtotal').textContent    = '₹' + subtotal.toFixed(2);
    document.getElementById('display-discount').textContent    = '-₹' + discountAmt.toFixed(2);
    document.getElementById('display-gst').textContent         = '+₹' + gstAmt.toFixed(2);
    document.getElementById('display-grand-total').textContent = '₹' + grandTotal.toFixed(2);
}

document.addEventListener('input', function(e) {
    if (e.target.matches('.item-qty, .item-price, #discount_percent, #gst_percent')) recalculate();
});

document.getElementById('add-item').addEventListener('click', function() {
    const div = document.createElement('div');
    div.className = 'item-row row align-items-center mx-0';
    div.innerHTML = `
        <div class="col-5 px-1"><input type="text" name="items[${itemIndex}][description]" class="form-control form-control-sm" placeholder="Description" required></div>
        <div class="col-2 px-1"><input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm item-qty" value="1" min="0.01" step="0.01" required></div>
        <div class="col-2 px-1"><input type="number" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm item-price" value="0" min="0" step="0.01" required></div>
        <div class="col-2 px-1"><input type="text" class="form-control form-control-sm item-total" readonly value="0.00"></div>
        <div class="col-1 px-1 text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button></div>
    `;
    document.getElementById('items-container').appendChild(div);
    itemIndex++;
});

document.getElementById('items-container').addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        if (document.querySelectorAll('.item-row').length > 1) {
            e.target.closest('.item-row').remove();
            recalculate();
        }
    }
});

recalculate();
</script>
@endsection

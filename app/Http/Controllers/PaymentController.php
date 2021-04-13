<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Invoice $invoice)
    {
        $invoice->load('user', 'payments');
        return view('payments.create', compact('invoice'));
    }

    public function store(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount_paid'      => 'required|numeric|min:0.01',
            'payment_type'     => 'required|in:cash,cheque,bank_transfer,upi,card',
            'payment_date'     => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
        ]);

        $invoice->load('payments');
        $balance = $invoice->grand_total - $invoice->payments->sum('amount_paid');

        if ($request->amount_paid > $balance + 0.01) {
            return back()->withErrors(['amount_paid' => 'Payment exceeds balance due of ₹' . number_format($balance, 2)])->withInput();
        }

        Payment::create([
            'invoice_id'       => $invoice->id,
            'amount_paid'      => $request->amount_paid,
            'payment_type'     => $request->payment_type,
            'payment_date'     => $request->payment_date,
            'reference_number' => $request->reference_number,
            'notes'            => $request->notes,
        ]);

        // Check if fully paid
        $totalPaid = $invoice->payments()->sum('amount_paid');
        if ($totalPaid >= $invoice->grand_total) {
            $invoice->update([
                'status'       => 'paid',
                'payment_type' => $request->payment_type,
                'payment_date' => $request->payment_date,
                'is_overdue'   => false,
            ]);
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment recorded successfully.');
    }

    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;
        $payment->delete();

        // If invoice was paid but now total paid < grand_total, revert to sent
        $totalPaid = $invoice->payments()->sum('amount_paid');
        if ($totalPaid < $invoice->grand_total && $invoice->status === 'paid') {
            $invoice->update(['status' => 'sent']);
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment deleted.');
    }
}

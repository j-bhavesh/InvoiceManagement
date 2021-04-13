<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\InvoiceItem;
use App\Mail\InvoiceMail;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->latest()->paginate(15)->appends($request->all());
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $users          = User::orderBy('name')->get();
        $invoiceNumber  = Invoice::generateInvoiceNumber();
        return view('invoices.create', compact('users', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'invoice_date'     => 'required|date',
            'due_date'         => 'required|date|after_or_equal:invoice_date',
            'gst_percent'      => 'required|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'notes'            => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $totals = $this->calculateTotals($request);

        $invoice = Invoice::create([
            'invoice_number'   => Invoice::generateInvoiceNumber(),
            'user_id'          => $request->user_id,
            'status'           => 'draft',
            'invoice_date'     => $request->invoice_date,
            'due_date'         => $request->due_date,
            'subtotal'         => $totals['subtotal'],
            'discount_percent' => $request->discount_percent ?? 0,
            'discount_amount'  => $totals['discount_amount'],
            'gst_percent'      => $request->gst_percent,
            'gst_amount'       => $totals['gst_amount'],
            'grand_total'      => $totals['grand_total'],
            'notes'            => $request->notes,
            'is_overdue'       => false,
        ]);

        foreach ($request->items as $item) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total'       => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('user', 'items', 'payments');
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice)->with('error', 'Paid invoices cannot be edited.');
        }
        $users = User::orderBy('name')->get();
        $invoice->load('items');
        return view('invoices.edit', compact('invoice', 'users'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice)->with('error', 'Paid invoices cannot be edited.');
        }

        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'invoice_date'     => 'required|date',
            'due_date'         => 'required|date|after_or_equal:invoice_date',
            'gst_percent'      => 'required|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'notes'            => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $totals = $this->calculateTotals($request);

        $invoice->update([
            'user_id'          => $request->user_id,
            'invoice_date'     => $request->invoice_date,
            'due_date'         => $request->due_date,
            'subtotal'         => $totals['subtotal'],
            'discount_percent' => $request->discount_percent ?? 0,
            'discount_amount'  => $totals['discount_amount'],
            'gst_percent'      => $request->gst_percent,
            'gst_amount'       => $totals['gst_amount'],
            'grand_total'      => $totals['grand_total'],
            'notes'            => $request->notes,
        ]);

        $invoice->items()->delete();
        foreach ($request->items as $item) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total'       => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function send(Invoice $invoice)
    {
        $invoice->update(['status' => 'sent']);
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice marked as sent.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load('user', 'items', 'payments');
        $pdf = PDF::loadView('pdf.invoice', compact('invoice'));
        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function sendEmail(Invoice $invoice)
    {
        $invoice->load('user', 'items', 'payments');
        $pdf = PDF::loadView('pdf.invoice', compact('invoice'));
        $pdfContent = $pdf->output();

        Mail::to($invoice->user->email)->send(new InvoiceMail($invoice, $pdfContent));

        if ($invoice->status === 'draft') {
            $invoice->update(['status' => 'sent']);
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice emailed to ' . $invoice->user->email);
    }

    private function calculateTotals(Request $request): array
    {
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $discountPercent = $request->discount_percent ?? 0;
        $discountAmount  = round($subtotal * $discountPercent / 100, 2);
        $afterDiscount   = $subtotal - $discountAmount;
        $gstAmount       = round($afterDiscount * $request->gst_percent / 100, 2);
        $grandTotal      = $afterDiscount + $gstAmount;

        return compact('subtotal', 'discountAmount', 'gstAmount', 'grandTotal');
    }
}

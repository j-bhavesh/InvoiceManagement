<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Mark overdue invoices
        Invoice::where('status', '!=', 'paid')
            ->where('due_date', '<', Carbon::today())
            ->update(['is_overdue' => true]);

        $totalInvoices  = Invoice::count();
        $paidInvoices   = Invoice::where('status', 'paid')->count();
        $pendingAmount  = Invoice::where('status', '!=', 'paid')->sum('grand_total');
        $overdueCount   = Invoice::where('is_overdue', true)->where('status', '!=', 'paid')->count();

        $recentInvoices = Invoice::with('user')
            ->latest()
            ->take(10)
            ->get();

        $overdueInvoices = Invoice::with('user')
            ->where('is_overdue', true)
            ->where('status', '!=', 'paid')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalInvoices', 'paidInvoices', 'pendingAmount',
            'overdueCount', 'recentInvoices', 'overdueInvoices'
        ));
    }
}

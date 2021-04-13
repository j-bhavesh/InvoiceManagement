<?php

namespace App\Console\Commands;

use App\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    protected $signature   = 'invoices:mark-overdue';
    protected $description = 'Mark unpaid invoices as overdue if due_date has passed';

    public function handle()
    {
        $count = Invoice::where('status', '!=', 'paid')
            ->where('due_date', '<', Carbon::today())
            ->where('is_overdue', false)
            ->update(['is_overdue' => true]);

        $this->info("Marked {$count} invoice(s) as overdue.");

        return 0;
    }
}

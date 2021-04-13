<?php

namespace App\Mail;

use App\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    protected string $pdfContent;

    public function __construct(Invoice $invoice, string $pdfContent)
    {
        $this->invoice    = $invoice;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this
            ->subject('Invoice ' . $this->invoice->invoice_number . ' from InvoicePro')
            ->view('emails.invoice')
            ->attachData(
                $this->pdfContent,
                'Invoice-' . $this->invoice->invoice_number . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}

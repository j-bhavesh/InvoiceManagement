<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'description', 'quantity', 'unit_price', 'total',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

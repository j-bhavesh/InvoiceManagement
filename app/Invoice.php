<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'user_id', 'status', 'invoice_date', 'due_date',
        'subtotal', 'discount_percent', 'discount_amount', 'gst_percent',
        'gst_amount', 'grand_total', 'notes', 'payment_type', 'payment_date', 'is_overdue',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date'     => 'date',
        'payment_date' => 'date',
        'is_overdue'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments->sum('amount_paid');
    }

    public function getBalanceDueAttribute()
    {
        return $this->grand_total - $this->total_paid;
    }

    public function getIsFullyPaidAttribute()
    {
        return $this->total_paid >= $this->grand_total;
    }

    public static function generateInvoiceNumber()
    {
        $last = static::latest('id')->first();
        $next = $last ? ($last->id + 1) : 1;
        return 'INV-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}

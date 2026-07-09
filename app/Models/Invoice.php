<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'user_id', 'payment_id', 'invoice_number', 'amount',
        'tax_amount', 'total', 'currency', 'status', 'pdf_path',
        'billing_details', 'issued_at', 'due_at', 'paid_at',
    ];
    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'tax_amount' => 'decimal:2', 'total' => 'decimal:2', 'billing_details' => 'array', 'issued_at' => 'datetime', 'due_at' => 'datetime', 'paid_at' => 'datetime'];
    }
    public function order() { return $this->belongsTo(Order::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function payment() { return $this->belongsTo(Payment::class); }

    public static function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'user_id', 'transaction_id', 'cinetpay_transaction_id',
        'payment_token', 'amount', 'currency', 'method', 'channel',
        'status', 'cinetpay_status', 'cinetpay_response', 'payer_phone',
        'payer_name', 'description', 'paid_at', 'failed_at', 'failure_reason',
        'is_webhook_verified', 'idempotency_key',
    ];
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2', 'cinetpay_response' => 'array',
            'paid_at' => 'datetime', 'failed_at' => 'datetime',
            'is_webhook_verified' => 'boolean',
        ];
    }
    public function order() { return $this->belongsTo(Order::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function invoice() { return $this->hasOne(Invoice::class); }

    public function scopeCompleted($query) { return $query->where('status', 'completed'); }
    public function getIsPaidAttribute(): bool { return $this->status === 'completed'; }

    public function getChannelLabelAttribute(): string
    {
        return match($this->channel) {
            'ORANGE_CI' => 'Orange Money',
            'MTN_CI' => 'MTN Money',
            'MOOV_CI' => 'Moov Money',
            'WAVE_CI' => 'Wave',
            'VISA' => 'Visa',
            'MASTERCARD' => 'Mastercard',
            default => $this->channel ?? 'N/A',
        };
    }

    public static function generateTransactionId(): string
    {
        return 'TXN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }
}

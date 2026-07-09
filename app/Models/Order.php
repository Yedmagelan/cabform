<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id', 'partner_id', 'coupon_id', 'order_number',
        'subtotal', 'discount_amount', 'tax_amount', 'total',
        'currency', 'status', 'notes', 'billing_info',
    ];
    protected function casts(): array
    {
        return ['subtotal' => 'decimal:2', 'discount_amount' => 'decimal:2', 'tax_amount' => 'decimal:2', 'total' => 'decimal:2', 'billing_info' => 'array'];
    }
    public function user() { return $this->belongsTo(User::class); }
    public function partner() { return $this->belongsTo(Partner::class); }
    public function coupon() { return $this->belongsTo(Coupon::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function invoice() { return $this->hasOne(Invoice::class); }
    public function enrollments() { return $this->hasMany(Enrollment::class); }

    public function scopePaid($query) { return $query->where('status', 'paid'); }
    public function scopePending($query) { return $query->where('status', 'pending'); }

    public function getIsPaidAttribute(): bool { return $this->status === 'paid'; }
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 0, ',', ' ') . ' FCFA';
    }

    public static function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}

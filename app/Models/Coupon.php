<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = [
        'code', 'name', 'description', 'type', 'value', 'min_order_amount',
        'max_discount_amount', 'max_uses', 'max_uses_per_user', 'used_count',
        'starts_at', 'expires_at', 'is_active', 'applicable_courses', 'applicable_categories',
    ];
    protected function casts(): array
    {
        return ['value' => 'decimal:2', 'min_order_amount' => 'decimal:2', 'max_discount_amount' => 'decimal:2', 'starts_at' => 'datetime', 'expires_at' => 'datetime', 'is_active' => 'boolean', 'applicable_courses' => 'array', 'applicable_categories' => 'array'];
    }
    public function orders() { return $this->hasMany(Order::class); }
    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeValid($query) {
        return $query->active()
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->where(fn($q) => $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses'));
    }
    public function calculateDiscount(float $subtotal): float
    {
        $discount = $this->type === 'percentage' ? ($subtotal * $this->value / 100) : $this->value;
        if ($this->max_discount_amount) $discount = min($discount, $this->max_discount_amount);
        return min($discount, $subtotal);
    }
}

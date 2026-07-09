<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'subtitle', 'image', 'link_url', 'link_text', 'position', 'sort_order', 'is_active', 'starts_at', 'ends_at'];
    protected function casts(): array { return ['is_active' => 'boolean', 'starts_at' => 'datetime', 'ends_at' => 'datetime']; }
    public function scopeActive($query) { return $query->where('is_active', true)->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now())); }
    public function scopeHero($query) { return $query->where('position', 'hero'); }
}

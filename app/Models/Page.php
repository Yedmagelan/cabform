<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['title', 'slug', 'content', 'meta_title', 'meta_description', 'featured_image', 'status', 'sort_order', 'show_in_menu', 'template'];
    protected function casts(): array { return ['show_in_menu' => 'boolean']; }
    public function scopePublished($query) { return $query->where('status', 'published'); }
    public function scopeInMenu($query) { return $query->published()->where('show_in_menu', true)->orderBy('sort_order'); }
}

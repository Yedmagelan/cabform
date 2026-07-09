<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['author_id', 'category_id', 'title', 'slug', 'excerpt', 'content', 'featured_image', 'meta_title', 'meta_description', 'tags', 'status', 'published_at', 'views_count', 'allow_comments'];
    protected function casts(): array { return ['tags' => 'array', 'published_at' => 'datetime', 'allow_comments' => 'boolean']; }
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
    public function category() { return $this->belongsTo(Category::class); }
    public function scopePublished($query) { return $query->where('status', 'published')->where('published_at', '<=', now()); }
    public function getReadTimeAttribute(): int { return max(1, intval(str_word_count(strip_tags($this->content)) / 200)); }
}

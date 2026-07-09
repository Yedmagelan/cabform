<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'instructor_id', 'title', 'slug', 'subtitle',
        'description', 'content', 'objectives', 'prerequisites',
        'target_audience', 'thumbnail', 'preview_video', 'level',
        'language', 'price', 'sale_price', 'is_free', 'duration_hours',
        'max_students', 'is_certified', 'sequential_unlock', 'is_self_paced',
        'status', 'published_at', 'rating', 'rating_count',
        'enrollment_count', 'version', 'meta_data',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'rating' => 'decimal:2',
            'is_free' => 'boolean',
            'is_certified' => 'boolean',
            'sequential_unlock' => 'boolean',
            'is_self_paced' => 'boolean',
            'published_at' => 'datetime',
            'meta_data' => 'array',
        ];
    }

    // ── Relations ────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('sort_order');
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Module::class)->orderBy('modules.sort_order')->orderBy('lessons.sort_order');
    }

    public function sessionsCohorts()
    {
        return $this->hasMany(SessionCohort::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function forumThreads()
    {
        return $this->hasMany(ForumThread::class);
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->published()->where('is_certified', true)->orderByDesc('rating');
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%");
        });
    }

    // ── Accessors ────────────────────────────────────────────────

    public function getEffectivePriceAttribute(): float
    {
        if ($this->is_free) return 0;
        return $this->sale_price ?? $this->price;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->has_discount || $this->price == 0) return 0;
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getTotalLessonsAttribute(): int
    {
        return $this->modules->sum(fn($m) => $m->lessons->count());
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->is_free) return 'Gratuit';
        return number_format($this->effective_price, 0, ',', ' ') . ' FCFA';
    }

    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            'debutant' => 'Débutant',
            'intermediaire' => 'Intermédiaire',
            'avance' => 'Avancé',
            'expert' => 'Expert',
            default => $this->level,
        };
    }
}

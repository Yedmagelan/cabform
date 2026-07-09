<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'title', 'slug', 'description',
        'sort_order', 'duration_minutes', 'is_free_preview', 'is_active', 'unlock_conditions',
    ];

    protected function casts(): array
    {
        return [
            'is_free_preview' => 'boolean',
            'is_active' => 'boolean',
            'unlock_conditions' => 'array',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getTotalDurationAttribute(): int
    {
        return $this->lessons->sum('duration_minutes');
    }
}

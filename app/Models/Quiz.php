<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'module_id', 'title', 'description', 'instructions',
        'type', 'duration_minutes', 'passing_score', 'max_attempts',
        'shuffle_questions', 'shuffle_answers', 'show_correct_answers',
        'show_score_immediately', 'questions_per_attempt', 'is_mandatory',
        'is_active', 'sort_order', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'passing_score' => 'decimal:2',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'show_correct_answers' => 'boolean',
            'show_score_immediately' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function course() { return $this->belongsTo(Course::class); }
    public function module() { return $this->belongsTo(Module::class); }
    public function questions() { return $this->hasMany(Question::class)->orderBy('sort_order'); }
    public function attempts() { return $this->hasMany(QuizAttempt::class); }

    public function scopeActive($query) { return $query->where('is_active', true); }

    public function getTotalPointsAttribute(): float
    {
        return $this->questions->sum('points');
    }
}

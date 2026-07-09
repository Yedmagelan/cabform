<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'quiz_id', 'attempt_number', 'score', 'max_score',
        'percentage', 'passed', 'answers_data', 'status', 'started_at',
        'submitted_at', 'graded_at', 'graded_by', 'feedback', 'time_spent_seconds',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'passed' => 'boolean',
            'answers_data' => 'array',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function grader() { return $this->belongsTo(User::class, 'graded_by'); }

    public function scopeSubmitted($query) { return $query->where('status', 'submitted'); }
    public function scopeGraded($query) { return $query->where('status', 'graded'); }
}

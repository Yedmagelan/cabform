<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id', 'question_text', 'explanation', 'type',
        'image', 'points', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function answers() { return $this->hasMany(Answer::class)->orderBy('sort_order'); }
    public function correctAnswers() { return $this->answers()->where('is_correct', true); }

    public function scopeActive($query) { return $query->where('is_active', true); }
}

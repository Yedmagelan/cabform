<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $table = 'progress';

    protected $fillable = [
        'user_id', 'lesson_id', 'enrollment_id', 'status',
        'time_spent_seconds', 'video_position_seconds',
        'completion_percentage', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completion_percentage' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function lesson() { return $this->belongsTo(Lesson::class); }
    public function enrollment() { return $this->belongsTo(Enrollment::class); }

    public function scopeCompleted($query) { return $query->where('status', 'completed'); }
}

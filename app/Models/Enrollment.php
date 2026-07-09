<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_id', 'session_cohort_id', 'order_id', 'partner_id',
        'status', 'progress_percentage', 'enrolled_at', 'started_at',
        'completed_at', 'expires_at', 'last_accessed_at', 'last_lesson_id',
        'time_spent_minutes', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'decimal:2',
            'enrolled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
            'last_accessed_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function sessionCohort() { return $this->belongsTo(SessionCohort::class, 'session_cohort_id'); }
    public function order() { return $this->belongsTo(Order::class); }
    public function partner() { return $this->belongsTo(Partner::class); }
    public function progress() { return $this->hasMany(Progress::class); }
    public function certificate() { return $this->hasOne(Certificate::class); }

    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeCompleted($query) { return $query->where('status', 'completed'); }

    public function getIsCompletedAttribute(): bool { return $this->status === 'completed'; }
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

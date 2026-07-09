<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionCohort extends Model
{
    use HasFactory;

    protected $table = 'sessions_cohorts';

    protected $fillable = [
        'course_id', 'name', 'description', 'start_date', 'end_date',
        'enrollment_deadline', 'max_students', 'enrolled_count', 'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'enrollment_deadline' => 'date',
        ];
    }

    public function course() { return $this->belongsTo(Course::class); }
    public function enrollments() { return $this->hasMany(Enrollment::class, 'session_cohort_id'); }

    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeUpcoming($query) { return $query->where('status', 'upcoming'); }

    public function getIsFullAttribute(): bool
    {
        return $this->max_students && $this->enrolled_count >= $this->max_students;
    }
}

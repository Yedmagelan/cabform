<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id', 'module_id', 'lesson_id', 'title', 'description',
        'instructions', 'max_score', 'passing_score', 'due_date',
        'max_file_size_mb', 'allowed_file_types', 'max_submissions', 'is_active', 'sort_order', 'rubric',
    ];
    protected function casts(): array
    {
        return ['max_score' => 'decimal:2', 'passing_score' => 'decimal:2', 'due_date' => 'datetime', 'allowed_file_types' => 'array', 'is_active' => 'boolean', 'rubric' => 'array'];
    }
    public function course() { return $this->belongsTo(Course::class); }
    public function module() { return $this->belongsTo(Module::class); }
    public function lesson() { return $this->belongsTo(Lesson::class); }
    public function submissions() { return $this->hasMany(Submission::class); }
    public function scopeActive($query) { return $query->where('is_active', true); }
}

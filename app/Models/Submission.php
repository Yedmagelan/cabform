<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;
    protected $fillable = [
        'assignment_id', 'user_id', 'content', 'file_path', 'file_name',
        'score', 'passed', 'feedback', 'rubric_grades', 'status', 'graded_by', 'submitted_at', 'graded_at',
    ];
    protected function casts(): array
    {
        return ['score' => 'decimal:2', 'passed' => 'boolean', 'submitted_at' => 'datetime', 'graded_at' => 'datetime', 'rubric_grades' => 'array'];
    }
    public function assignment() { return $this->belongsTo(Assignment::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function grader() { return $this->belongsTo(User::class, 'graded_by'); }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumThread extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'course_id', 'lesson_id', 'title', 'slug', 'body', 'is_pinned', 'is_locked', 'is_resolved', 'replies_count', 'views_count', 'last_reply_at'];
    protected function casts(): array { return ['is_pinned' => 'boolean', 'is_locked' => 'boolean', 'is_resolved' => 'boolean', 'last_reply_at' => 'datetime']; }
    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function lesson() { return $this->belongsTo(Lesson::class); }
    public function posts() { return $this->hasMany(ForumPost::class, 'thread_id'); }
}

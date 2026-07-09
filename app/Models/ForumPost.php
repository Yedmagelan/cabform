<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['thread_id', 'user_id', 'parent_id', 'body', 'is_best_answer', 'likes_count'];
    protected function casts(): array { return ['is_best_answer' => 'boolean']; }
    public function thread() { return $this->belongsTo(ForumThread::class, 'thread_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function parent() { return $this->belongsTo(ForumPost::class, 'parent_id'); }
    public function replies() { return $this->hasMany(ForumPost::class, 'parent_id'); }
}

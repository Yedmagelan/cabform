<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'avatar',
        'status',
        'two_factor_enabled',
        'two_factor_secret',
        'google_id',
        'facebook_id',
        'last_login_at',
        'last_login_ip',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
        ];
    }

    /**
     * Accesseur : nom complet.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Accesseur : initiales.
     */
    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    // ── Relations ────────────────────────────────────────────────

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function partner()
    {
        return $this->hasOne(Partner::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function forumThreads()
    {
        return $this->hasMany(ForumThread::class);
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInstructors($query)
    {
        return $query->role('formateur');
    }

    public function scopeLearners($query)
    {
        return $query->role('apprenant');
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->hasRole('administrateur');
    }

    public function isInstructor(): bool
    {
        return $this->hasRole('formateur');
    }

    public function isLearner(): bool
    {
        return $this->hasRole('apprenant');
    }

    public function isManager(): bool
    {
        return $this->hasRole('gestionnaire');
    }

    public function isPartner(): bool
    {
        return $this->hasRole('partenaire');
    }

    public function enrolledIn(Course $course): bool
    {
        return $this->enrollments()->where('course_id', $course->id)->whereIn('status', ['active', 'completed'])->exists();
    }

    public function getUnreadMessagesCountAttribute(): int
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'course_id', 'enrollment_id', 'certificate_template_id',
        'certificate_number', 'hash', 'qr_code_path', 'pdf_path',
        'verification_url', 'final_score', 'status', 'issued_at',
        'expires_at', 'revoked_at', 'revocation_reason',
    ];
    protected function casts(): array
    {
        return ['final_score' => 'decimal:2', 'issued_at' => 'datetime', 'expires_at' => 'datetime', 'revoked_at' => 'datetime'];
    }
    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function enrollment() { return $this->belongsTo(Enrollment::class); }
    public function template() { return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id'); }

    public function scopeGenerated($query) { return $query->where('status', 'generated'); }
    public function getIsValidAttribute(): bool
    {
        return $this->status === 'generated' && (!$this->expires_at || $this->expires_at->isFuture());
    }
}

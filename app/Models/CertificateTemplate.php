<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'description', 'background_image', 'logo_path',
        'signature_image', 'signatory_name', 'signatory_title',
        'layout_config', 'is_default', 'is_active',
    ];
    protected function casts(): array
    {
        return ['layout_config' => 'array', 'is_default' => 'boolean', 'is_active' => 'boolean'];
    }
    public function certificates() { return $this->hasMany(Certificate::class); }
    public function scopeActive($query) { return $query->where('is_active', true); }
}

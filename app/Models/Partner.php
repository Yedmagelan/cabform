<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'company_name', 'registration_number', 'tax_id',
        'address', 'city', 'country', 'phone', 'email', 'website',
        'logo', 'description', 'contact_person', 'contact_phone',
        'max_learners', 'status', 'contract_start', 'contract_end',
    ];

    protected function casts(): array
    {
        return [
            'contract_start' => 'date',
            'contract_end' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

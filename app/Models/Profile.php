<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date_of_birth', 'gender', 'nationality', 'address',
        'city', 'country', 'postal_code', 'company', 'job_title',
        'bio', 'linkedin_url', 'website_url', 'education_level', 'interests',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'interests' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

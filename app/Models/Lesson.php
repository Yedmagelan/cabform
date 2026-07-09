<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id', 'title', 'slug', 'description', 'content',
        'type', 'video_url', 'video_provider', 'duration_minutes',
        'sort_order', 'is_free_preview', 'is_downloadable', 'is_active', 'meta_data', 'unlock_conditions',
    ];

    protected function casts(): array
    {
        return [
            'is_free_preview' => 'boolean',
            'is_downloadable' => 'boolean',
            'is_active' => 'boolean',
            'meta_data' => 'array',
            'unlock_conditions' => 'array',
        ];
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class)->orderBy('sort_order');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function getCourseAttribute()
    {
        return $this->module->course;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'video' => 'fa-play-circle',
            'text' => 'fa-file-alt',
            'pdf' => 'fa-file-pdf',
            'audio' => 'fa-headphones',
            'slide' => 'fa-presentation',
            'embed' => 'fa-code',
            'scorm' => 'fa-cube',
            default => 'fa-file',
        };
    }
}

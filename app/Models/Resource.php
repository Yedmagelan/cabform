<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id', 'title', 'file_path', 'file_name',
        'file_type', 'file_size', 'type', 'is_downloadable', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_downloadable' => 'boolean'];
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}

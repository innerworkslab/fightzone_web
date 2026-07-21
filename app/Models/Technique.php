<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technique extends Model
{
    use HasFactory;

    protected $fillable = [
        'technique_category_id',
        'name',
        'description',
        'thumbnail_url',
        'url',
        'provider',
        'provider_video_id',
        'duration',
        'is_active',
    ];

    protected $casts = [
        'technique_category_id' => 'integer',
        'duration' => 'string',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'duration_seconds',
        'formatted_duration',
    ];

    public function category()
    {
        return $this->belongsTo(TechniqueCategory::class, 'technique_category_id');
    }

    public function getDurationSecondsAttribute()
    {
        if (!$this->duration) {
            return 0;
        }

        $parts = explode(':', $this->duration);
        if (count($parts) === 3) {
            return ((int) $parts[0] * 3600)
                + ((int) $parts[1] * 60)
                + (int) $parts[2];
        }

        return 0;
    }

    public function getFormattedDurationAttribute()
    {
        $seconds = $this->duration_seconds;

        if ($seconds === 0) {
            return '00:00:00';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}

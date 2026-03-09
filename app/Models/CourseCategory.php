<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_path',
        'is_active',
    ];

    protected $hidden = [
        'image_path'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url'
    ];

    /**
     * Get all courses in this category
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function getImageUrlAttribute()
    {
        if($this->image_path){
            return Storage::url($this->image_path);
        }
        return null;
    }
}

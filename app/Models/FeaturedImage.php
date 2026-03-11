<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FeaturedImage extends Model
{
    //
    protected $fillable = [
        'image_path'
    ];

    protected $hidden = [
        'image_path'
    ];

    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        if($this->image_path){
            return Storage::url($this->image_path);
        }
        return null;
    }
}

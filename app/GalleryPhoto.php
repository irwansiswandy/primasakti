<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GalleryPhoto extends Model
{
    protected $fillable = [
      'file_name',
      'caption',
      'file_path',
      'thumb_path'
    ];

    public function category()
    {
      return $this->belongsToMany('App\GalleryCategory', 'gallery_category_photo');
    }
}

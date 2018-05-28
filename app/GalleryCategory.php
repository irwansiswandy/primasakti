<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GalleryCategory extends Model
{
    protected $fillable = [
      'name'
    ];

    public function photos()
    {
      return $this->belongsToMany('App\GalleryPhoto', 'gallery_category_photo');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPhoto extends Model
{
    protected $fillable = [
    	'path',
    	'thumb_path',
    	'caption'
    ];

    public function user()
    {
    	return $this->belongsToMany('App\User', 'user_user_photo');
    }
}
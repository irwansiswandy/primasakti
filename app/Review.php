<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
    	'title',
    	'description',
    	'score'
    ];

    public function user() {
    	return $this->belongsToMany('App\User', 'review_user');
    }
}

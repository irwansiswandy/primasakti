<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name'];

    public function setNameAttribute($name)
    {
    	return $this->attributes['name'] = strtoupper($name);
    }

    public function country()
    {
    	return $this->belongsToMany('App\Country', 'country_state');
    }

    public function city()
    {
    	return $this->belongsToMany('App\City', 'city_state');
    }
}

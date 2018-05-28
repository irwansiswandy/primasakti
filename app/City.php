<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
	protected $fillable = ['name', 'phonecode'];

	public function setNameAttribute($name)
	{
		return $this->attributes['name'] = strtoupper($name);
	}

	public function state()
	{
		return $this->belongsToMany('App\City', 'city_state');
	}
}

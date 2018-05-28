<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['code', 'name', 'phonecode'];

    public function setCodeAttribute($code)
    {
    	return $this->attributes['code'] = strtoupper($code);
    }

    public function setNameAttribute($name)
    {
    	return $this->attributes['name'] = strtoupper($name);
    }

    public function state()
    {
    	return $this->belongsToMany('App\State', 'country_state');
    }
}

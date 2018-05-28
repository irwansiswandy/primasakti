<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
    	'name',
    	'option_id',
        'staff_bonus'
    ];

    // SET METHODS
    public function setNameAttribute($name)
    {
    	return $this->attributes['name'] = strtoupper($name);
    }

    // GET METHODS
    public function getNameAttribute($name)
    {
        return $this->attributes['name'] = strtoupper($name);
    }

    // RELATIONSHIP METHODS
    public function products()
    {
    	return $this->hasMany('App\Product');
    }

    public function options()
    {
        return $this->hasMany('App\ProductOption');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProductCategory;

class Product extends Model
{
    protected $fillable = [
        'product_category_id',
    	'name',
    	'price1',
    	'qty1',
    	'price2',
    	'qty2',
    	'price3',
    	'qty3',
        'price4',
        'qty4'
    ];

    // SET ATTRIBUTES METHODS
    public function setNameAttribute($name)
    {
        return $this->attributes['name'] = strtoupper($name);
    }

    // RELATIONSHIP METHODS
    public function category()
    {
    	return $this->belongsTo('App\ProductCategory', 'product_category_id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    public function price_histories()
    {
        return $this->hasMany('App\PriceHistory');
    }
}

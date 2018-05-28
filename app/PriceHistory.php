<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $fillable = [
    	'product_id',
    	'price1',
    	'qty1',
    	'price2',
    	'qty2',
    	'price3',
    	'qty3',
    	'price4',
    	'qty4'
    ];

    // RELATIONSHIP METHODS
    public function product()
    {
    	return $this->belongsTo('App\Product', 'product_id');
    }
}

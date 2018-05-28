<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
    	'order_id',
    	'category_id',
    	'description'
    ];

    /* SET ATTRIBUTE METHODS */

    public function setDescriptionAttribute($description)
    {
        return $this->attributes['description'] = strtoupper($description);
    }

    /* END: SET ATTRIBUTE METHODS */

    /* RELATIONSHIP METHODS */

    public function order()
    {
    	return $this->belongsTo('App\Order', 'order_id');
    }

    public function categories()
    {
    	return $this->belongsTo('App\ProductCategory', 'category_id');
    }

    /* END: RELATIONSHIP METHODS */
}

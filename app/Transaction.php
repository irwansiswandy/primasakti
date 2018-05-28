<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ProductCategory;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'qty',
        'price',
        'sub_total',
        'staff_bonus'
    ];

    protected $dates = [
        'deleted_at'
    ];

    // RELATIONSHIP METHODS
    public function invoice()
    {
    	return $this->belongsTo('App\Invoice', 'invoice_id');
    }
    public function product()
    {
    	return $this->belongsTo('App\Product', 'product_id');
    }

    // OTHER METHODS
    public function staff_bonus()
    {
        $transaction_total = $this->qty * $this->price;

        $product = Product::findOrFail($this->product_id);
        $category_id = $product->product_category_id;

        $category = ProductCategory::findOrFail($category_id);
        $staff_bonus = $category->staff_bonus;

        return ($staff_bonus/100) * $transaction_total;
    }
}

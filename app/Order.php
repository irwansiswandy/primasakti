<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    protected $fillable = [
    	'order_no',
    	'staff_id',
    	'user_id',
    	'note',
    	'down_payment',
    	'deadline',
        'finished_at',
        'status'
    ];

    /* SET METHODS */

    public function setNoteAttribute($note)
    {
        return $this->attributes['note'] = strtoupper($note);
    }

    /* END: SET METHODS */

    /* GET METHODS */

    public function getStatusAttribute($status)
    {
        if ($status == 0) {
            return 'PENDING';
        }
        else if ($status == 1) {
            return 'PROCESSED';
        }
        else if ($status == 2) {
            return 'FINISHED';
        }
        else {
            return 'STATUS_CODE_ERROR';
        }
    }

    /* END: GET METHODS */

    /* RELATIONSHIP METHODS */

    public function order_details()
    {
    	return $this->hasMany('App\OrderDetail');
    }

    public function staff()
    {
    	return $this->belongsTo('App\User', 'staff_id');
    }

    public function user()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function invoice()
    {
    	return $this->belongsTo('App\Invoice', 'invoice_id');
    }

    /* END: RELATIONSHIP METHODS */
}

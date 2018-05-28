<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ShopSchedule extends Model
{
    protected $fillable = [
    	'day',
    	'open_hour',
    	'open_minute',
    	'closed_hour',
    	'closed_minute'
    ];

    public function open()
    {
    	$open = Carbon::createFromTime($this->open_hour, $this->open_minute, 0, 'Asia/Jakarta');
    	return $open->format('h:i A');
    }

    public function closed()
    {
    	$closed = Carbon::createFromTime($this->closed_hour, $this->closed_minute, 0, 'Asia/Jakarta');
    	return $closed->format('h:i A');
    }
}

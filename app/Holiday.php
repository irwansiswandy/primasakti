<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    protected $fillable = ['date', 'theme'];

    public function setDateAttribute($date)
    {
      return $this->attributes['date'] = Carbon::parse($date);
    }
    public function getDateAttribute($date)
    {
      return $this->attributes['date'] = Carbon::createFromTimestamp(strtotime($date));
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Invoice extends Model
{
    protected $fillable = [
      'staff_id',
      'user_id',
      'invoice_no',
      'total',
      'paid',
      'change',
      'payment_status',
      'payment_deadline',
      'staff_bonus'
    ];

    protected $dates = [
      'deleted_at'
    ];

    // SET ATTRIBUTE METHODS
    public function setPaymentStatusAttribute($payment_status)
    {
        return $this->attributes['payment_status'] = strtoupper($payment_status);
    }

    // SCOPE METHODS
    public function scopeToday($query)
    {
      $start = Carbon::now()->startOfDay();
      $end = Carbon::now()->endOfDay();
      return $this->whereBetween('created_at', [$start, $end]);
    }
    public function scopeYesterday($query)
    {
      $start = Carbon::now()->startOfDay()->subDay();
      $end = Carbon::now()->endOfDay()->subDay();
      return $this->whereBetween('created_at', [$start, $end]);
    }
    public function scopeThisWeek($query)
    {
      $start = Carbon::now()->startOfWeek();
      $end = Carbon::now()->endOfWeek();
      return $this->whereBetween('created_at', [$start, $end]);
    }
    public function scopeLastWeek($query)
    {
      $start = Carbon::now()->startOfWeek()->subWeek();
      $end = Carbon::now()->endOfWeek()->subWeek();
      return $this->whereBetween('created_at', [$start, $end]);
    }
    public function scopeThisMonth($query)
    {
      $start = Carbon::now()->startOfMonth();
      $end = Carbon::now()->endOfMonth();
      return $query->whereBetween('created_at', [$start, $end]);
    }
    public function scopeLastMonth($query)
    {
      $start = Carbon::now()->startOfMonth()->subMonth();
      $end = Carbon::now()->endOfMonth()->subMonth();
      return $query->whereBetween('created_at', [$start, $end]);
    }

    // RELATIONSHIP METHODS
    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }
    public function products()
    {
        return $this->hasManyThrough('App\Product', 'App\Transaction', 'invoice_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function staff()
    {
        return $this->belongsTo('App\User', 'staff_id');
    }
}

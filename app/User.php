<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

class User extends Authenticatable
{
    protected $fillable = [
      'firstname',
      'lastname',
      'email',
      'password',
      'address',
      'city',
      'state',
      'postcode',
      'country',
      'phone',
      'cellphone'
    ];

    protected $hidden = [
      'password',
      'verification_token',
      'remember_token'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function($user) {
            $user->verification_token = str_random(100);
        });
    }

    // SET ATTRIBUTE METHODS
    public function setFirstnameAttribute($firstname)
    {
        return $this->attributes['firstname'] = strtoupper($firstname);
    }
    public function setLastnameAttribute($lastname)
    {
        return $this->attributes['lastname'] = strtoupper($lastname);
    }
    public function setPasswordAttribute($password)
    {
        return $this->attributes['password'] = bcrypt($password);
    }
    public function setAddressAttribute($address)
    {
      return $this->attributes['address'] = strtoupper($address);
    }
    public function setCityAttribute($city)
    {
      return $this->attributes['city'] = strtoupper($city);
    }
    public function setStateAttribute($state)
    {
      return $this->attributes['state'] = strtoupper($state);
    }
    public function setUserLevelAttribute($user_level)
    {
      if ($user_level == 1 || strtoupper($user_level) == 'USER') {
        return $this->attributes['user_level'] = 1;
      }
      else if ($user_level == 2 || strtoupper($user_level) == 'STAFF') {
        return $this->attributes['user_level'] = 2;
      }
      else if ($user_level == 3 || strtoupper($user_level) == 'ADMIN') {
        return $this->attributes['user_level'] = 3;
      }
      else if ($user_level == 4 || strtoupper($user_level) == 'SUPERVISOR') {
        return $this->attributes['user_level'] = 4;
      }
    }

    // GET ATTRIBUTE METHODS
    public function getUserLevelAttribute($user_level)
    {
        /*
          USER LEVELS :
          1 -> USER
          2 -> STAFF
          3 -> SUPERVISOR
          4 -> MANAGER
        */
        if ($user_level == 1) {
          return $user_level = 'USER';
        }
        else if ($user_level == 2) {
          return $user_level = "STAFF";
        }
        else if ($user_level == 3) {
          return $user_level = "ADMIN";
        }
        else if ($user_level == 4) {
          return $user_level = "SUPERVISOR";
        }
    }
    public function getAddressAttribute($address)
    {
      return $this->attributes['address'] = nl2br($address);
    }

    // SCOPE METHODS
    public function scopeUser($query)
    {
      return $query->where('user_level', 1);
    }
    public function scopeStaff($query)
    {
      return $query->whereIn('user_level', [2, 4]);
    }
    public function scopeAdmin($query)
    {
      return $query->where('user_level', 3);
    }

    // RELATIONSHIP METHODS
    public function review()
    {
      return $this->belongsToMany('App\Review', 'review_user');
    }
    public function photo()
    {
      return $this->belongsToMany('App\UserPhoto', 'user_user_photo');
    }
    public function working_team()
    {
      return $this->belongsToMany('App\WorkingTeam', 'user_working_team');
    }
    public function user_invoices()
    {
      return $this->hasMany('App\Invoice', 'user_id');
    }
    public function staff_invoices()
    {
      return $this->hasMany('App\Invoice', 'staff_id');
    }

    // OTHER METHODS
    public function is_user()
    {
      if ($this->user_level == 'USER') {
        return true;
      }
      else {
        return false;
      }
    }
    public function is_staff()
    {
      if ($this->user_level == 'STAFF') {
        return true;
      }
      else if ($this->user_level == 'SUPERVISOR') {
        return true;
      }
      else {
        return false;
      }
    }
    public function is_admin()
    {
      if ($this->user_level == 'ADMIN') {
        return true;
      }
      else {
        return false;
      }
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class WorkingTeam extends Model
{
    protected $fillable = [
    	'name'
    ];

    // SET ATTRIBUTES METHODS
    public function setNameAttribute($name)
    {
    	return $this->name = strtoupper($name);
    }

    // RELATIONSHIP METHODS
    public function staff()
    {
    	return $this->belongsToMany('App\User', 'user_working_team');
    }
}
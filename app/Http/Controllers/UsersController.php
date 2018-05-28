<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;

class UsersController extends Controller
{
    public function index()
    {
    	return view('admin.users');
    }

    public function fetch_all_users() {
    	$users = User::all();
    	return $users;
    }
}

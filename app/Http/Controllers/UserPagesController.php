<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\UserDetails;
use App\Review;
use Auth;

class UserPagesController extends Controller
{
    public function index($user_id)
    {
    	$user = User::findOrFail($user_id);
    	return view('user.dashboard', compact('user'));
    }

    public function main($user_id)
    {
        $user = User::findOrFail($user_id);
        return view('user.main', compact('user'));   
    }

    public function my_profile($user_id)
    {
        $user = User::findOrFail($user_id);
        return view('user.my_profile', compact('user'));
    }

    public function review($user_id)
    {
        $user = User::findOrFail($user_id);
        return view('user.review', compact('user'));
    }

    public function post_review(Request $request, $user_id)
    {
      $review = Review::create($request->all());
      $review->user()->attach($user_id);
      $user = User::findOrFail($user_id);
      $user->wrote_review = true;
      $user->save();
    }

    public function fetchProfile($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->user_details;
        return $user;
    }

    public function logout()
    {
    	Auth::logout();
    	return redirect()->action('PagesController@main');
    }
}

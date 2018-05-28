<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\UserDetails;
use Session;
use Auth;
use Mail;
use Carbon\Carbon;
use Event;
use App\Events\NewUserRegistered;

class MyAuthController extends Controller
{
    public function register_form()
    {
    	return view('myauth.register_form');
    }

    public function register_post(Request $request)
    {
    	// VALIDATE REGISTER FORM FIELDS
        $this->validate($request, [
      		'firstname' 			=> 'required',
      		'lastname'  			=> 'required',
      		'email'					=> 'required|email|unique:users,email',
      		'password'				=> 'required',
      		'password_confirmation' => 'required|same:password',
      		'address'				=> 'required',
      		'city'					=> 'required',
      		'state'					=> 'required',
      		'country'				=> 'required',
            'country_phonecode'     => 'required',
            'city_phonecode'        => 'required',
      		'phone'					=> 'required',
      		'cellphone'				=> 'required'
		]);

        $firstname = $request->input('firstname');
        $lastname  = $request->input('lastname');
        $email     = $request->input('email');
        $password  = $request->input('password_confirmation');
        $address   = $request->input('address');
        $city      = $request->input('city');
        $postcode  = $request->input('postcode');
        $state     = $request->input('state');
        $country1  = $request->input('country');
        $country2  = explode(",", $country1);
        $country   = $country2[1];
        $phone     = $request->input('country_phonecode').$request->input('city_phonecode').$request->input('phone');
        $cellphone = $request->input('country_phonecode').$request->input('cellphone');

        // STORE USER DATA TO DB
        User::create([
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'email'     => $email,
            'password'  => $password,
            'address'   => $address,
            'city'      => $city,
            'state'     => $state,
            'postcode'  => $postcode,
            'country'   => $country,
            'phone'     => $phone,
            'cellphone' => $cellphone
        ]);

        // TRIGGER EVENT: NewUserRegistered, THEN CALL LISTENER: EmailVerification
        Event::fire(new NewUserRegistered($email));

        // SET FLASH MESSAGE
        flash()->success('Registrasi Berhasil', 'Silahkan cek e-mail anda untuk verifikasi');

        // REDIRECT BACK TO REGISTER FORM WITH FLASH MESSAGE: Registration Success
        return redirect()->back();
    }

    public function register_verify_email($user_verification_token, $user_id)
    {
        $user = User::findOrFail($user_id);

        if ($user->verified == 1)
        {
            flash()->success('Account anda sudah aktif', 'Tidak perlu melakukan verifikasi e-mail lagi, anda bisa langsung login');
            return redirect()->action('MyAuthController@login_form');
        }
        else
        {
            $verify_user = $user->where('verification_token', $user_verification_token)->update([
                'verified' => true,
                'verification_token' => null
            ]);

            if ($verify_user) {
                Mail::send('emails.post_verified_email', ['user' => $user], function($message) use ($user) {
                    $message->from('primasakti1.sby@gmail.com');
                    $message->subject('[Primasakti] A user account has just been verified');
                    $message->to('irwansiswandymks@gmail.com');
                });

                flash()->success('Verifikasi E-mail Berhasil', 'Account anda sudah bisa digunakan untuk login');
                return redirect()->action('MyAuthController@login_form');
            }
            else
            {
                flash()->error('Verifikasi E-mail Gagal', 'Silahkan cek kembali link verifikasi yang kami kirimkan ke e-mail anda');
                return redirect()->action('PagesController@main');
            }
        }
    }

    public function verify_as_staff($user_id, $user_firstname, $user_lastname)
    {
        $user = User::where(['id' => $user_id, 'firstname' => $user_firstname, 'lastname' => $user_lastname])->firstOrFail();
        $user->user_level = 2;
        $user->updated_at = Carbon::now();
        $user->save();

        Mail::send('emails.staff_approved_email', ['user' => $user], function($message) use ($user) {
            $message->from('primasakti1.sby@gmail.com');
            $message->subject('[Primasakti] A new staff has just been approved');
            $message->to('irwansiswandymks@gmail.com');
        });
    }

    public function login_form()
    {
        return view('myauth.login_form');
    }

    public function login(Request $request)
    {
        // VALIDATING LOGIN FORM
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // INITIALIZE VARIABLES FOR E-MAIL AND PASSWORD
        $email = $request->input('email');
        $password = $request->input('password');
        $find_email = User::where('email', $email)->get();

        if (count($find_email) <= 0) {
            flash()->info_error('E-mail belum terdaftar', 'Anda belum bisa login, silahkan lakukan registrasi');
            return redirect()->action('MyAuthController@login_form');
        }
        else {
            $check_login = Auth::attempt(['email' => $email, 'password' => $password]);
            if ($check_login == true)
            {
                if (Auth::user()->verified == true)
                {
                    if (Auth::user()->user_level == 'ADMIN')
                    {
                        flash()->success('Login Berhasil', 'Halo Admin !');

                        return redirect()->intended(action('AdminPagesController@index'));
                    }
                    else if (Auth::user()->user_level == 'STAFF' || Auth::user()->user_level == 'SUPERVISOR')
                    {
                        flash()->success('Login Berhasil', 'Halo Staff !\n\n'.Auth::user()->firstname.' '.Auth::user()->lastname);
                        return redirect()->intended(action('StaffPagesController@index', Auth::user()->id));
                    }
                    else
                    {
                        $message = Auth::user()->firstname.' '.Auth::user()->lastname;
                        $user = Auth::user()->id;
                        return redirect()->intended(action('UserPagesController@index', Auth::user()->id));
                    }
                }
                else
                {
                    flash()->info_info('Account anda belum aktif', 'Silahkan lakukan verifikasi e-mail sebelum login');
                    return redirect()->action('MyAuthController@login_form');
                }
            }
            else
            {
                flash()->error('Login Gagal', 'Silahkan cek kembali e-mail dan password anda');
                return redirect()->action('MyAuthController@login_form');
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->action('PagesController@main');
    }

    public function reset_password_form()
    {
        return view('myauth.forgot_password');
    }

    public function get_countries()
    {
        return Country::all();
    }

    public function get_idStates()
    {
        return State::id_states_all();
    }
}

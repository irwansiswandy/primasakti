<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Carbon\Carbon;
use File;
use Session;
use Vinkla\Pusher\Facades\Pusher;

use App\Holiday;
use App\ShopSchedule;
use App\GalleryCategory;
use App\GalleryPhoto;
use App\User;
use App\UserDetails;
use App\Review;
use App\Order;

class PagesController extends Controller
{
  public function test_pusher_client()
  {
    return view('test_pusher');
  }

  public function test_pusher_server()
  {
    Pusher::trigger('my_channel', 'my_event', ['message' => 'Hello']);
  }

  public function main()
  {
    return view('pages.main');
  }

  public function track_order(Request $request)
  {
    $this->validate($request, [
      'order_no' => 'required'
    ]);

    $track_order = Order::where('order_no', $request->order_no)->first();
    if ($track_order) {
      return response($track_order, 200);
    }
    else {
      return response('Data tidak ada', 200);
    }
  }

  public function fetch_server_time()
  {
    $server_time = Carbon::now();
    return response([
      'server_time' => $server_time
    ], 200);
  }

  public function fetch_business_days()
  {
    $business_days = ShopSchedule::all();
    return $business_days;
  }

  public function fetch_today_schedule()
  {
    $today = Carbon::now()->dayOfWeek;
    $today_schedule = ShopSchedule::where('day', $today)->get(['open_hour', 'open_minute', 'closed_hour', 'closed_minute']);

    return $today_schedule[0];
  }

  public function location()
  {
    return view('pages.location');
  }

  public function email_form()
  {
    return view('pages.email_us');
  }

  public function email_send(Request $request)
  {
    // INITIALIZE SENDER VARIABLES
    $email_category = $request->input('about');
    $sender_name    = $request->input('name');
    $sender_company = $request->input('company');
    $sender_email   = $request->input('email');
    $sender_contact = $request->input('contact');
    $sender_message = $request->input('message');
    $sender_files   = $request->file('file');

    // INITIALIZE A VARIABLE FOR TOTAL FILES -> THIS WILL BE USED FOR SENDING E-MAIL ATTACHMENTS
    $count_files = count(array_filter($sender_files));

    // VALIDATE FORM FIELDS EXCEPT FOR FILES
    $this->validate($request, [
      'name'    => 'required',
      'email'   => 'required|email',
      'contact' => 'required',
      'message' => 'required'
    ]);

    if ($count_files != null)
    {
      // VALIDATE FILES FIELD -> NOT SET YET -> BE CAREFUL !

      // MAKE A CACHE DIRECTORY "GuestCache" IF IT DOESN'T EXIST
      if (!file_exists('GuestCache'))
      {
        mkdir('GuestCache', 0777, true);
      }

      // MOVE SENDER_FILE TO CACHE FOLDER NAMED "GuestCache"
      foreach ($sender_files as $sender_file)
      {
        $sender_file->move('GuestCache/', time().'-'.$sender_file->getClientOriginalName());
      }

      // CREATE PATHS TO CACHED FILES
      foreach ($sender_files as $sender_file)
      {
        $path_to_temp_sender_file[] = public_path('GuestCache\\'.time().'-'.$sender_file->getClientOriginalName());
      }

      // SEND E-MAIL TO ADMIN
      Mail::send('emails.email_us', [
        'email_category' => $email_category,
        'sender_name'    => $sender_name,
        'sender_company' => $sender_company,
        'sender_email'   => $sender_email,
        'sender_contact' => $sender_contact,
        'sender_message' => $sender_message
      ], function ($message) use($email_category, $sender_name, $sender_files, $path_to_temp_sender_file, $count_files) {
          $message->to('irwansiswandymks@gmail.com', 'Irwan Siswandy')
                  ->from('primasakti1.sby@gmail.com', 'PRIMASAKTI-MAILER')
                  ->subject('['.$email_category.']'.' by '.$sender_name);
          for ($x = 0; $x < $count_files; $x++)
          {
            $message->attach($path_to_temp_sender_file[$x]);
          }
      });

      // DELETE CACHE FILES IN FOLDER "GuestCache"
      for ($x = 0; $x < $count_files; $x++)
      {
        File::delete($path_to_temp_sender_file[$x]);
      }
    }
    else if ($count_files == null)
    {
      // SEND E-MAIL TO ADMIN
      Mail::send('emails.email_us', [
        'email_category' => $email_category,
        'sender_name'    => $sender_name,
        'sender_company' => $sender_company,
        'sender_email'   => $sender_email,
        'sender_contact' => $sender_contact,
        'sender_message' => $sender_message
      ], function ($message) use($email_category, $sender_name) {
          $message->to('irwansiswandymks@gmail.com', 'Irwan Siswandy')
                  ->from('primasakti1.sby@gmail.com', 'PRIMASAKTI-MAILER')
                  ->subject('['.$email_category.']'.' by '.$sender_name);
      });
    }

    // FLASH MESSAGE FOR SUCCESS OR FAILED
    $flash_message = "We will get back to soon";
    Session::flash('flash_message', $flash_message);

    // RETURN BACK TO MAINPAGE
    return redirect()->action('PagesController@email_form');
  }

  public function products_gallery()
  {
    // QUERY FOR ALL CATEGORIES
    $categories = GalleryCategory::all();
    // REDIRECT TO GALLERY PAGE
    return view('pages.gallery');
  }

  public function fetch_all_categories_with_photos()
  {
    $categories = GalleryCategory::with('photos')->get();
    return $categories;
  }
}

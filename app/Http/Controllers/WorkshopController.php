<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Mail;
use Vinkla\Pusher\Facades\Pusher;

use App\User;
use App\ProductCategory;
use App\Order;
use App\OrderDetail;

class WorkshopController extends Controller
{
    public function order_list()
    {
        return view('workshop.order_list');
    }

    public function fetch_orders()
    {
        $orders = Order::with('order_details.categories', 'staff.working_team', 'user')->get();
        return response($orders, 200);
    }

    public function order_in()
    {
    	return view('workshop.order_in');
    }

    public function add_order(Request $request)
    {
        // STORE NEW ORDER TO DB
        $new_order = Order::create($request->all());

        if ($new_order) {
            // STORE ORDER DETAILS TO DB
            for ($i=0; $i<count($request->itemIds); $i++) {
                $new_order_detail = OrderDetail::create([
                    'order_id' => $new_order->id,
                    'category_id' => $request->itemIds[$i],
                    'description' => $request->itemDescriptions[$i]
                ]);
            }

            // SET PUSHER TO BROADCAST NEWLY ADDED ORDER
            $broadcasted_order = Order::where('id', $new_order->id)->with('order_details.categories', 'staff.working_team', 'user')->first();
            Pusher::trigger('primasakti_channel', 'new_order_added', $broadcasted_order);

            // SET FLASH MESSAGE
            flash()->success('Selesai', 'Order berhasil disimpan');
            
            return response([], 201);
        }
        else {
            // ROLLBACK NEW ORDER
            $rollback_new_order = Order::where('order_no', $request->order_no)->delete();
            
            return response([], 500);
        }
    }

    public function fetch_today_date()
    {
        $today = Carbon::now();
        return response($today, 200);
    }

    public function get_users()
    {
    	$users = User::user()->where('firstname', '<>', 'N/A')
                             ->orderBy('firstname')
                             ->get(['id', 'firstname', 'lastname', 'email', 'phone', 'cellphone', 'address', 'city', 'state']);
    	return response($users, 200);
    }

    public function get_staffs()
    {
    	$staffs = User::Staff()->orderBy('firstname')->with('working_team')->get();
    	return response($staffs, 200);
    }

    public function get_items()
    {
        $items = ProductCategory::all();
        return response($items, 200);
    }

    public function register_new_user(Request $request)
    {
        $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users,email',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'phone' => 'required',
            'cellphone' => 'required'
        ]);

        // SET DEFAULT PASSWORD
        $default_password = str_random(8);

        // STORE NEW USER TO DB
        $new_user = User::create([
            'firstname' => strtoupper($request->firstname),
            'lastname' => strtoupper($request->lastname),
            'email' => $request->email,
            'password' => $default_password,
            'address' => strtoupper($request->address),
            'city' => strtoupper($request->city),
            'state' => strtoupper($request->state),
            'country' => strtoupper($request->country),
            'phone' => $request->phone,
            'cellphone' => $request->cellphone
        ]);

        // SEND E-MAIL TO INFORM NEW USER ABOUT HIS/HER USER ACCOUNT
        send_email()->new_user_registered_at_workshop($request->email);

        // SET FLASH MESSAGE
        flash()->success('Berhasil', 'Data user baru telah berhasil disimpan');

        // RETURN RESPONSE TO VIEW
        return response([], 201);
    }
}

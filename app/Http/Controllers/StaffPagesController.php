<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Auth;
use DB;
use Vinkla\Pusher\Facades\Pusher;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\ProductCategory;
use App\Product;
use App\Invoice;
use App\Transaction;
use App\WorkingTeam;
use App\Order;
use App\OrderDetail;

class StaffPagesController extends Controller
{
	public function index()
	{
		return redirect()->action('StaffPagesController@dashboard', Auth::id());
	}

	public function dashboard($staff_id)
	{
		$staff = User::findOrFail($staff_id);
		return view('staff.dashboard', compact('staff'));
	}

	public function dashboard_fetch_info_staff_data($staff_id)
	{
		$staff = User::Staff()->where('id', $staff_id)->with('working_team.staff')->first();
		return response($staff, 200);
	}

	public function dashboard_fetch_this_month_sales_data($staff_id)
	{
		$teams = WorkingTeam::with('staff')->get();

		// GET EACH TEAM STAFF IDS
		for ($i=0; $i<count($teams); $i++) {
			if (count($teams[$i]->staff) < 1) {
				$staff_ids[$i] = [];
			}
			else {
				for ($j=0; $j<count($teams[$i]->staff); $j++) {
					$staff_ids[$i][$j] = $teams[$i]->staff[$j]->id;
				}
			}
		}

		// GET EACH TEAM SALES
		for ($i=0; $i<count($staff_ids); $i++) {
			if (count($staff_ids[$i]) < 1) {
				$team_sales[$i] = 0;
			}
			else {
				for ($j=0; $j<count($staff_ids[$i]); $j++) {
					$team_sales[$i] = Invoice::ThisMonth()->whereIn('staff_id', $staff_ids[$i])->sum('total');
				}
			}
		}

		for ($i=0; $i<count($teams); $i++) {
			$team_names[$i] = $teams[$i]->name;
		}

		return response([
			'team_names' => $team_names,
			'team_sales' => $team_sales
		], 200);
	}

	public function order_list_fetch_orders($staff_id)
	{
		$orders = Order::where(['taken' => false])->with('order_details.categories', 'staff.working_team', 'user')->get();
		return response($orders, 200);
	}

	public function order_list_update(Request $request, $staff_id, $url_name)
	{
		if ($url_name == 'a_job_taken') {
			$order = Order::where(['order_no' => $request->order_no])->update([
				'staff_id' => $request->staff_id
			]);
		}
		else if ($url_name == 'a_job_cancelled') {
			$order = Order::where(['order_no' => $request->order_no, 'staff_id' => $staff_id])->update([
				'staff_id' => $request->staff_id
			]);
		}
		else if ($url_name == 'a_job_processed') {
			$order = Order::where(['order_no' => $request->order_no, 'staff_id' => $staff_id])->update([
				'status' => $request->status
			]);
		}
		else if ($url_name == 'cancel_job_processing') {
			$order = Order::where(['order_no' => $request->order_no, 'staff_id' => $staff_id])->update([
				'status' => $request->status
			]);
		}
		else if ($url_name == 'a_job_finished') {
			$order = Order::where(['order_no' => $request->order_no, 'staff_id' => $staff_id])->update([
				'status' => $request->status,
				'finished_at' => $request->finished_at
			]);
		}
		else if ($url_name == 'order_received_by_customer') {
			$order = Order::where(['order_no' => $request->order_no])->update([
				'taken' => $request->taken,
				'taken_at' => $request->taken_at
			]);
		}
	}

	public function profile($staff_id)
	{
		return view('staff.profile');
	}

	public function price_list()
    {
        return view('staff.price_list');
    }

    public function bonus_table()
    {
    	return view('staff.bonus_table');
    }

	public function pos_system()
	{
		return view('staff.pos');
	}

	public function pos_get_users()
	{
		$users = User::User()->where('id', '<>', '1')->get();
		return response($users, 200);
	}

	public function pos_fetch_team_mate($staff_id)
	{
		$logged_in_staff = User::find($staff_id);

		// CHECK STAFF'S LEVEL
		// IF STAFF LEVEL = STAFF
		if ($logged_in_staff->user_level == 2 || $logged_in_staff->user_level == 'STAFF') {
			$team = User::find($staff_id)->working_team;
			if (count($team) <= 0) {
				$team_members = [];
			}
			else {
				$team_id = User::find($staff_id)->working_team[0]->id;
				$team_members = WorkingTeam::find($team_id)->staff;
			}
			return response($team_members, 200);
		}
		// IF STAFF LEVEL = SUPERVISOR
		else if ($logged_in_staff->user_level == 4 || $logged_in_staff->user_level == 'SUPERVISOR') {
			$teammates = User::Staff()->get();
			return response($teammates, 200);
		}
		else {
			return response([], 200);
		}
	}

	public function pos_get_categories()
	{
		$categories = ProductCategory::all();
		return response($categories, 200);
	}

	public function pos_get_products($category_id)
	{
		$products = ProductCategory::find($category_id)->products;
		return response($products, 200);
	}

	public function pos_get_option_category_name($category_id)
	{
		$category = ProductCategory::find($category_id);
		$category_name = $category->name;
		return response($category_name, 200);
	}

	// METHOD FOR STORING INVOICE-DATA TO TABLE: invoices
	public function pos_invoice_store_invoice(Request $request, $staff_id)
	{
		$add_invoice = Invoice::create($request->all());

		if ($add_invoice) {
			return response($add_invoice->id, 202);
		}
		else {
			return response(['error' => 'failed to store new invoice'], 500);
		}
	}

	// METHOD FOR STORING INVOICE-DATA TO TABLE: transactions
	public function pos_invoice_store_transaction(Request $request, $staff_id, $invoice_id)
	{
		$invoice_no = $request->invoice_no;
		$products_id = $request->product_id;
		$qtys = $request->qty;
		$prices = $request->price;

		// STORE TRANSACTIONS DATA TO DB
		for ($i=0; $i<count($products_id); $i++) {
			// SKIP EMPTY PRODUCTS
			if ($products_id[$i] != null)
			{
				$sub_total = $qtys[$i] * $prices[$i];
				$transaction_bonus[$i] = $this->transaction_bonus($products_id[$i], $qtys[$i], $prices[$i]);

				Transaction::create([
					'invoice_id' => $invoice_id,
					'product_id' => $products_id[$i],
					'qty' => $qtys[$i],
					'price' => $prices[$i],
					'sub_total' => $sub_total,
					'staff_bonus' => $transaction_bonus[$i]
				]);
			}
			else
			{
				continue;
			}
		}

		// INSERT STAFF BONUS TO SELECTED INVOICE ID
		$invoice_bonus = array_sum($transaction_bonus);
		Invoice::find($invoice_id)->update([
			'staff_bonus' => $invoice_bonus
		]);

		// TRIGGER PUSHER TO BROADCAST NEWLY ADDED INVOICE
		$pushed_invoice = Invoice::where('id', $invoice_id)->with(['transactions', 'staff.working_team', 'user'])->first();
		Pusher::trigger('primasakti_channel', 'new_invoice_added', $pushed_invoice);

		return flash()->success('Selesai', 'Transaksi telah berhasil disimpan');
	}

	// METHOD FOR CALCULATING STAFF_BONUS
	public function transaction_bonus($product_id, $qty, $price)
	{
		$category_id = Product::find($product_id)->category->id;
		$category_bonus = ProductCategory::find($category_id)->staff_bonus;
		$bonus_for_staff = ($qty * $price) * ($category_bonus/100);
		return $bonus_for_staff;
	}

	public function pos_cancel_invoice(Request $request)
	{
		$cancelled_invoice_no = $request->invoice_no;
		return Invoice::where('invoice_no', $cancelled_invoice_no)->first()->delete();
	}

	public function sales($staff_id)
	{
		return view('staff.sales');
	}

	public function get_sales($staff_id, $data_option)
	{
		$logged_in_staff = User::find($staff_id);
		if ($logged_in_staff->user_level == 2 || $logged_in_staff->user_level == 'STAFF') {
			return $this->get_sales_for_staff($staff_id, $data_option);
		}
		else if ($logged_in_staff->user_level == 4 || $logged_in_staff->user_level == 'SUPERVISOR') {
			return $this->get_sales_for_supervisor($staff_id, $data_option);
		}
		else {
			return response([], 401);
		}
	}

	public function sales_delete_invoice($invoice_id)
	{
		$deleted_invoice = Invoice::where('id', $invoice_id)->with(['transactions', 'staff.working_team', 'user'])->first();
		$data = $deleted_invoice;

		$delete_invoice = Invoice::find($invoice_id)->delete();

      	if ($delete_invoice) {
      		return [
      			Pusher::trigger('primasakti_channel', 'invoice_deleted', $data),
        		response([], 202)
        	];
      	}
      	else {
        	return response([], 500);
      	}
	}

	/* -------------------- PRIVATE METHODS -------------------- */

	private function get_sales_for_supervisor($staff_id, $data_option)
	{
		// GET CURRENT YEAR
		$year = Carbon::now()->year;

		// GET SELECTED MONTH
		if ($data_option == 'LastMonth') {
			$month = Carbon::now()->subMonth()->month;
		}
		else {
			$month = Carbon::now()->month;
		}

		// GET STAFF & WORKING_TEAM DATA
		$staff_data = User::where('id', $staff_id)->first();

		// GET INVOICES
		$per_page = 15;
		$invoices_with_pagination = Invoice::$data_option()->with('transactions.product.category', 'staff.working_team', 'user')
										   ->orderBy('created_at', 'desc')
										   ->paginate($per_page);

		// CALCULATE INVOICES
		$invoices = Invoice::$data_option()->get();
		$total_sales = $this->count_total_sales($invoices);

		// CALCULATE SUPERVISOR'S BONUS
		$total_bonus = $total_sales * (1/100);

		// GET ALL TEAMS AND ITS MEMBER IDS
		$teams = WorkingTeam::all();
		for ($i=0; $i<count($teams); $i++) {
			$team_names[] = $teams[$i]->name;
			$staff_ids[] = $this->get_staff_ids($teams[$i]->id);
		}

		// CALCULATE ALL TEAMS TOTAL SALES
		for ($i=0; $i<count($staff_ids); $i++) {
			$team_sales[] = Invoice::$data_option()->whereIn('staff_id', $staff_ids[$i])->sum('total');
		}

		// MAKE NEW JSON OBJECTS FOR OTHER_TEAMS_DATA
		for ($i=0; $i<count($team_names); $i++) {
			$other_teams_data[$i] = [
				'team' => $team_names[$i],
				'total_sales' => $team_sales[$i]
			];
		}

		// GET ALL STAFF AND CALCULATE THEIR SALES
		$staffs = User::Staff()->where('user_level', 2)->get(['id']);
		for ($i=0; $i<count($staffs); $i++) {
			$staff = User::Staff()->where('id', $staffs[$i]->id)->first();
			$staff_names[$i] = $staff->firstname.' '.$staff->lastname;
			$staff_sales[$i] = Invoice::$data_option()->where('staff_id', $staffs[$i]->id)->sum('total');
		}

		// MAKE NEW JSON OBJECTS FOR INDIVIDUAL_SALES_DATA
		for ($i=0; $i<count($staffs); $i++) {
			$each_staff_sales[$i] = [
				'staff_name' => $staff_names[$i],
				'total_sales' => $staff_sales[$i]
			];
		}

		return response([
			'month' => $month,
			'year' => $year,
			'staff_data' => $staff_data,
			'invoices' => $invoices_with_pagination,
			'total_sales' => $total_sales,
			'total_bonus' => $total_bonus,
			'other_teams_data' => $other_teams_data,
			'individual_sales_data' => $each_staff_sales
		], 200);
	}

	private function get_sales_for_staff($staff_id, $data_option)
	{
		// GET CURRENT YEAR
		$year = Carbon::now()->year;

		// GET SELECTED MONTH
		if ($data_option == 'LastMonth') {
			$month = Carbon::now()->subMonth()->month;
		}
		else {
			$month = Carbon::now()->month;
		}

		// GET STAFF & WORKING_TEAM DATA
		$staff_data = User::where('id', $staff_id)->with('working_team.staff')->first();
		$staff_team = $staff_data->working_team;

		/* CHECK IF STAFF HAS WORKING_TEAM
		   IF YES -> GET ALL STAFF_IDS IN WORKING_TEAM
		   IF NOT -> SKIP AND SET STAFF_IDS TO ONLY STAFF_ID */
		if (count($staff_team) <= 0) {
			$team_id = null;
			$staff_ids[] = $staff_id;
		}
		else {
			// GET ALL TEAM MEMBERS ID -> STORE TO A NEW ARRAY
			$team_id = User::find($staff_id)->working_team[0]->id;
			$team_staff_array = WorkingTeam::find($team_id)->staff;

			for ($i=0; $i<count($team_staff_array); $i++) {
				$staff_ids[] = $team_staff_array[$i]->id;
			}
		}

		// GET PAGINATED TEAM INVOICES
		$per_page = 15;

		$invoices_with_pagination = Invoice::$data_option()->whereIn('staff_id', $staff_ids)
													   	   ->with('transactions.product.category', 'staff.working_team', 'user')
													       ->orderBy('created_at', 'desc')
													       ->paginate($per_page);

		// CALCULATE TEAM INVOICES
		$invoices = Invoice::$data_option()->whereIn('staff_id', $staff_ids)->get();
		$total_sales = $this->count_total_sales($invoices);
		$total_bonus = $this->count_total_bonus($invoices);

		/* CHECK IF STAFF IS A MEMBER OF A WORKING_TEAM
		   IF YES -> GET OTHER TEAMS,
		   IF NOT -> GET ALL TEAMS */
		if ($team_id == null) {
			$other_teams = WorkingTeam::with('staff')->get();
		}
		else {
			// GET OTHER TEAM IDS
			$other_teams = WorkingTeam::where('id', '<>', $team_id)->with('staff')->get();
		}

		for ($i=0; $i<count($other_teams); $i++) {
			$other_team_ids[$i] = $other_teams[$i]->id;
			$other_team_names[$i] = $other_teams[$i]->name;

			/* CHECK IF A TEAM HAS MEMBERS
			   IF YES -> DO LOOP TO GET MEMBER IDS
			   IF NOT -> SKIP LOOP -> SET EMPTY ARRAY TO $other_team_staff_ids */
			if (count($other_teams[$i]->staff) > 0) {
				for ($j=0; $j<count($other_teams[$i]->staff); $j++) {
					$other_team_staff_ids[$i][$j] = $other_teams[$i]->staff[$j]->id;
				}
			}
			else {
				$other_team_staff_ids[$i] = [];
			}
		}

		// GET OTHER TEAM INVOICES
		for ($i=0; $i<count($other_team_staff_ids); $i++) {
			$other_team_invoices[$i] = Invoice::$data_option()->whereIn('staff_id', $other_team_staff_ids[$i])->get();
		}

		// CALCULATE TOTAL OF OTHER TEAM INVOICES
		for ($i=0; $i<count($other_team_invoices); $i++) {
			if (count($other_team_invoices[$i]) > 0) {
				$other_team_total_sales[$i] = $this->count_total_sales($other_team_invoices[$i]);
			}
			else {
				$other_team_total_sales[$i] = 0;
			}
		}

		// MAKE A NEW JSON ARRAY FOR OTHER TEAMS DATA
		for ($i=0; $i<count($other_teams); $i++) {
			$other_teams_data[] = [
				'team' => $other_team_names[$i],
				'total_sales' => $other_team_total_sales[$i]
			];
		}

		// GET EACH STAFF DATA & INVOICES
		for ($i=0; $i<count($staff_ids); $i++) {
			$staff_details[$i] = User::find($staff_ids[$i]);
			$each_staff_invoices[$i] = Invoice::$data_option()->where('staff_id', $staff_ids[$i])->get();
		}

		for ($i=0; $i<count($staff_details); $i++) {
			$each_staff_sales[$i] = [
				'staff_name' => $staff_details[$i]->firstname.' '.$staff_details[$i]->lastname,
				'total_sales' => $this->count_total_sales($each_staff_invoices[$i])
			];
		}

		return response([
			'month' => $month,
			'year' => $year,
			'staff_data' => $staff_data,
			'invoices' => $invoices_with_pagination,
			'total_sales' => $total_sales,
			'total_bonus' => $total_bonus,
			'other_teams_data' => $other_teams_data,
			'individual_sales_data' => $each_staff_sales
		], 200);
	}

	private function count_total_sales($invoices)
	{
		for ($i=0; $i<count($invoices); $i++) {
			$totals_array[] = $invoices[$i]->total;
		}

		if (empty($totals_array) == true) {
			$invoices_total = 0;
		}
		else {
			$invoices_total = array_sum($totals_array);
		}

		return $invoices_total;
	}

	private function count_total_bonus($invoices)
	{
		for ($i=0; $i<count($invoices); $i++) {
			$staff_bonuses_array[] = $invoices[$i]->staff_bonus;
		}

		if (empty($staff_bonuses_array) == true) {
			$staff_bonus_total = 0;
		}
		else {
			$staff_bonus_total = array_sum($staff_bonuses_array);
		}

		return $staff_bonus_total;
	}

	private function get_staff_ids($working_team_id)
	{
		$team = WorkingTeam::where('id', $working_team_id)->with('staff')->first();
		for ($i=0; $i<count($team->staff); $i++) {
			$staff_ids[] = $team->staff[$i]->id;
		}
		return $staff_ids;
	}
}

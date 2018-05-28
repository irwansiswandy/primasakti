<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use File;
use Carbon\Carbon;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Holiday;
use App\ShopSchedule;
use App\GalleryCategory;
use App\GalleryPhoto;
use App\ProductCategory;
use App\Product;
use App\User;
use App\WorkingTeam;
use App\Staff;
use App\Invoice;
use App\PriceHistory;

class AdminPagesController extends Controller
{
  /* START: DASHBOARD CONTROLLER */
  public function index()
  {
    return view('admin.dashboard');
  }

  public function dashboard_fetch_daily_sales_chart_data()
  {
    $current_date = Carbon::now()->setTime(0, 0, 0);
    $current_day = $current_date->day;
    $current_month = $current_date->month;
    $current_year = $current_date->year;

    $start_date = Carbon::create($current_year, $current_month, 1, 0, 0, 0);
    $start_day = 1;

    $end_date = Carbon::now()->endOfMonth();
    $end_day = $end_date->day;

    $total_days = $current_day - $start_day;

    for ($i=0; $i<=$total_days; $i++) {
      $start_time = Carbon::create($current_year, $current_month, $i+1, 0, 0, 0);
      $end_time = Carbon::create($current_year, $current_month, $i+1, 23, 59, 59);

      $days[$i] = $i+1;
      $dates[$i] = $start_time;
      $total_sales[$i] = Invoice::whereBetween('created_at', [$start_time, $end_time])->sum('total');
    }

    return response([
      'start_date' => $start_date,
      'end_date' => $end_date,
      'days' => $days,
      'dates' => $dates,
      'sales' => $total_sales
    ], 200);
  }

  public function dashboard_fetch_today_sales_data()
  {
    $today_invoices = Invoice::Today()->with('staff.working_team')->get();
    return response($today_invoices, 200);
  }
  /* END: DASHBOARD CONTROLLER */

// HOLIDAYS CONTROLLERS ------------------------------------------------------------------------------------------------

  public function holidays_form()
  {
    // INITIALIZE FORM TITLE AND FORM BUTTON FOR ADD HOLIDAY FORM
    $form_title   = 'Add Public Holiday';
    $button_title = 'Store This Date';

    // INITIALIZE HOLIDAYS LIST
    $holidays       = Holiday::orderBy('date')->get();
    $total_holidays = Holiday::all()->count();

    // BACK TO HOLIDAYS FORM
    return view('admin.holidays.holidays_add_form', compact('form_title', 'button_title', 'holidays', 'total_holidays'));
  }

  public function holiday_store(Request $request)
  {
    // VALIDATE FORM
    $this->validate($request, [
      'date'  => 'unique:holidays,date',
      'theme' => 'required'
    ]);

    // STORE HOLIDAY
    Holiday::create($request->all());

    // REDIRECT BACK TO HOLIDAYS FORM
    return redirect()->action('AdminPagesController@holidays_form');
  }

  public function holiday_edit($holiday_id)
  {
    // INITIALIZE FORM TITLE AND FORM BUTTON FOR EDIT HOLIDAY FORM
    $form_title   = 'Edit This Public Holiday';
    $button_title = 'Update This Date';

    // FIND HOLIDAY DATE THAT WANT TO BE EDITED
    $current_holiday = Holiday::find($holiday_id);

    $holidays = Holiday::orderBy('date')->get();
    $total_holidays = Holiday::all()->count();
    return view('admin.holidays.holidays_edit_form', compact(
      'form_title',
      'button_title',
      'current_holiday',
      'holidays',
      'total_holidays'
    ));
  }

  public function holiday_update(Request $request, $holiday_id)
  {
    $holiday = Holiday::find($holiday_id);
    $holiday->update($request->all());

    return redirect()->action('AdminPagesController@holidays_form');
  }

  public function holiday_delete($holiday_id)
  {
    // DELETE SELECTED HOLIDAY
    $holiday = Holiday::find($holiday_id);
    $holiday->delete();
    // REDIRECT BACK TO HOLIDAYS FORM
    return redirect()->action('AdminPagesController@holidays_form');
  }

// WORKING HOURS CONTROLLERS ------------------------------------------------------------------------------------------------

  public function working_days()
  {
    return view('admin/working_hours');
  }

  public function working_days_add(Request $request)
  {
    $working_day = ShopSchedule::create($request->all());
    if ($working_day) {
      return response(200);
    }
    else {
      return response(400);
    }
  }

  public function working_days_update(Request $request)
  {
    $working_day = ShopSchedule::where('day', $request->day)->firstOrFail();
    $working_day->update($request->all());
    return response(200);
  }

  // GALLERY CONTROLLERS ------------------------------------------------------------------------------------------------

  public function manage_gallery()
  {
    return view('admin/gallery');
  }

  public function gallery_store_category(Request $request)
  {
    $this->validate($request, [
      'name' => 'required'
    ]);

    GalleryCategory::create($request->all());

    return redirect()->action('AdminPagesController@gallery_add_category');
  }

  public function gallery_show($category_id)
  {
    // FIND SELECTED CATEGORY
    $category = GalleryCategory::find($category_id);

    // FIND SELECTED CATEGORY'S PHOTOS
    $photos   = GalleryPhoto::where('category_id', $category_id)->get();

    return view('admin.gallery.show', compact('category', 'photos'));
  }

  public function gallery_delete_category($category_id)
  {
    // FIND CATEGORY BY ID
    $deleted_category = GalleryCategory::find($category_id);

    // DELETE CATEGORY'S FOLDER
    File::deleteDirectory('gallery/'.$deleted_category->id.'-'.$deleted_category->name);

    // DELETE CATEGORY FROM DATABASE
    $deleted_category->delete();

    // DELETE CATEGORY'S PHOTOS FROM DATABASE
    GalleryPhoto::where('category_id', $category_id)->delete();

    return redirect()->action('AdminPagesController@gallery_add_category');
  }

  public function gallery_add_photos(Request $request, $category_id)
  {
    // FIND CATEGORY NAME
    $category      = GalleryCategory::find($category_id);
    $category_name = $category->id.'-'.$category->name;

    // INITIALIZE VARIABLES FOR PHOTOS
    $photos     = $request->file('file');
    $file_name  = time().'-'.$photos->getClientOriginalName();
    $thumb_name = 'thumb'.'-'.$file_name;

    // INITIALIZE VARIABLES FOR FILE & THUMBNAIL PATHS
    $file_path  = 'gallery'.'/'.$category_name.'/';
    $thumb_path = 'gallery'.'/'.$category_name.'/'.'thumbnails'.'/';

    // CHECK FOLDER "GALLERY" -> CREATE IF IT DOESN'T EXIST
    if (!file_exists('gallery'))
    {
      mkdir('gallery', 0777, true);
    }

    if (!file_exists($file_path))
    {
      mkdir($file_path, 0777, true);
    }

    // STORE PHOTOS TO FOLDER "GALLERY"
    $photos->move($file_path, $file_name);

    if (!file_exists($thumb_path))
    {
      mkdir($thumb_path, 0777, true);
    }

    // CREATE THUMBNAIL -> CREATE FOLDER "THUMBNAIL" -> SAVE THUMBNAIL
    $create_thumb = Image::make($file_path.$file_name);
    $create_thumb->fit(171, 180)->save($thumb_path.$thumb_name);

    // STORE FILE & THUMBNAIL PATHS TO DATABASE
    $paths              = new GalleryPhoto;
    $paths->category_id = $category_id;
    $paths->folder_name = $category_name;
    $paths->file_name   = $file_name;
    $paths->thumb_name  = $thumb_name;
    $paths->file_path   = $file_path.$file_name;
    $paths->thumb_path  = $thumb_path.$thumb_name;
    $paths->save();

    // LINK UPLOADED PHOTOS TO CATEGORY
    $paths->category()->attach($category_id);
  }

  public function gallery_delete_photo($category_id, $photo_id)
  {
    // FIND DELETED PHOTO IN DATABASE
    $selected_category = GalleryCategory::find($category_id);
    $selected_photo    = $selected_category->photos->find($photo_id);
    // DELETE PHOTO AND ITS THUMBNAIL FILES
    $photo_path = 'gallery\\'.$selected_category->id.'-'.$selected_category->name.'\\'.$selected_photo->file_name;
    $thumb_path = 'gallery\\'.$selected_category->id.'-'.$selected_category->name.'\\thumbnails\\'.$selected_photo->thumb_name;
    File::delete($photo_path, $thumb_path);
    // DELETE SELECTED PHOTO RECORDS FROM DATABASE
    $selected_photo->delete();
    // RELOAD PAGE
    return redirect()->action('AdminPagesController@gallery_show', $category_id);
  }

  public function categories_index()
  {
    return view('admin.products.categories');
  }

  public function categories_all()
  {
    // GET ALL CATEGORIES
    $categories = ProductCategory::with('products')->get();
    // CALCULATE NUMBER OF CATEGORIES
    $total_categories = count($categories);
    // CALCULATE NUMBER OF PRODUCTS
    for ($i=0; $i<$total_categories; $i++) {
      $products[$i] = count($categories[$i]->products);
    }
    $total_products = array_sum($products);
    // RETURN REQUESTED DATA TO VIEW
    return [
      'categories' => $categories,
      'total_categories' => $total_categories,
      'total_products' => $total_products
    ];
  }

  public function categories_add(Request $request)
  {
    return ProductCategory::create($request->all());
  }

  public function categories_show($id)
  {
    return ProductCategory::findOrFail($id);
  }

  public function categories_update(Request $request, $id)
  {
      $category = ProductCategory::findOrFail($id);
      $category->update($request->all());
  }

  public function categories_delete($id)
  {
    $delete_category = ProductCategory::find($id)->delete();
    if ($delete_category) {
      return response([], 200);
    }
    else {
      return response([], 500);
    }
  }

  public function products_index($id)
  {
    $category = ProductCategory::find($id);
    $category_name = strtoupper($category->name);

    return view('admin.products.products')->with(['category_name' => $category_name]);
  }

  public function products_all($id)
  {
    $products = Product::where('product_category_id', $id)->with('category')->get();
    return response($products, 200);
  }

  public function products_add(Request $request, $id)
  {
    $category = ProductCategory::find($id);
    $add_product = $category->products()->create($request->all());
    if ($add_product) {
      return response([], 201);
    }
    else {
      return response([], 500);
    }
  }

  public function products_show($category_id, $product_id)
  {
    $product = Product::findOrFail($product_id);
    return response()->json($product);
  }

  public function products_update(Request $request, $category_id, $product_id)
  {
    /* DESCRIPTION: UPDATE1 -> UPDATE WITHOUT ADDING TO PRICE HISTORY */
    $product = Product::find($product_id);

    $product->update([
      'name'   => $request->input('name'),
      'price1' => $request->input('price1'),
      'qty1'   => $request->input('qty1'),
      'price2' => $request->input('price2'),
      'qty2'   => $request->input('qty2'),
      'price3' => $request->input('price3'),
      'qty3'   => $request->input('qty3'),
      'price4' => $request->input('price4'),
      'qty4'   => $request->input('qty4')
    ]);
  }

  public function products_save_history(Request $request, $category_id, $product_id)
  {
    /* DESCRIPTION: UPDATE2 -> ADD PRICE TO HISTORY BEFORE UPDATING */
    $product = Product::find($product_id);

    return PriceHistory::create([
      'product_id' => $product->id,
      'price1' => $product->price1,
      'qty1' => $product->qty1,
      'price2' => $product->price2,
      'qty2' => $product->qty2,
      'price3' => $product->price3,
      'qty3' => $product->qty3,
      'price4' => $product->price4,
      'qty4' => $product->qty4
    ]);
  }

  public function products_delete($category_id, $product_id)
  {
    $delete_product = Product::find($product_id)->delete();
    if ($delete_product) {
        return response([], 200);
    }
    else {
      return response([], 500);
    }
  }

  public function users_view()
  {
    return view('admin.users');
  }

  public function users_get_working_team()
  {
    $supervisors = User::Staff()->where('user_level', 4)->get();
    $teams = WorkingTeam::with('staff')->get();

    $working_team = [
      'supervisors' => $supervisors,
      'teams' => $teams
    ];

    return response($working_team, 200);
  }

  public function users_update_level(Request $request)
  {
    $user_id = $request->id;
    $user_level = $request->user_level;

    $user = User::find($user_id);
    $user->user_level = $user_level;
    $user->save();

    return response([
      'user_id' => $user_id,
      'user_level' => $user_level
    ], 202);
  }

  public function users_update_team(Request $request)
  {
    $user_id = $request->id;
    $working_team_name = $request->name;

    $user = User::find($user_id);

    if ($user) {
      if ($working_team_name == null) {
        $user->working_team()->detach();
        return response(['user_id' => $user_id, 'team' => 'No Team'], 202);
      }
      else {
        if ($user->working_team == null) {
          $working_team = WorkingTeam::where('name', $working_team_name)->first()->staff()->attach($user_id);
          return response(['user_id' => $user_id, 'team' => $working_team_name], 202);
        }
        else {
          $user->working_team()->detach();
          $working_team = WorkingTeam::where('name', $working_team_name)->first()->staff()->attach($user_id);
          return response(['user_id' => $user_id, 'team' => $working_team_name], 202);
        }
      }
    }
    else {
      return response([], 404);
    }
  }

  /* ----- CONTROLLERS FOR SALES ----- */
  public function sales()
  {
    return view('admin/sales');
  }

  public function sales_get_sales($data_option)
  {
    // GET CURRENT MONTH & YEAR
    $current_month = Carbon::now()->month;
    $current_year = Carbon::now()->year;

    // SET PAGINATION PER_PAGE
    $data_per_page = 18;

    // GET ALL INVOICES REQUIRED BY $data_option
    $invoices_with_pagination = Invoice::$data_option()->with('transactions.product.category', 'staff.working_team', 'user')
                                       ->orderBy('created_at', 'desc')
                                       ->paginate($data_per_page);

    // CALCULATE $invoices TOTAL
    $invoices_without_pagination = Invoice::$data_option()->get();
    if (count($invoices_without_pagination) > 0) {
      for ($i=0; $i<count($invoices_without_pagination); $i++) {
        $invoice_totals_array[] = $invoices_without_pagination[$i]->total;
        $staff_bonuses_array[] = $invoices_without_pagination[$i]->staff_bonus;
      }
      $invoices_total = array_sum($invoice_totals_array);
      $staff_bonus_expense = array_sum($staff_bonuses_array);
    }
    else {
      $invoices_total = 0;
      $staff_bonus_expense = 0;
    }

    // GET ALL WORKING_TEAMS
    $teams = WorkingTeam::with('staff')->get();

    for ($i=0; $i<count($teams); $i++) {
      if (count($teams[$i]->staff) > 0) {
        for ($j=0; $j<count($teams[$i]->staff); $j++) {
          $each_member_total[$i][$j] = $this->count_invoices_total($data_option, $teams[$i]->staff[$j]->id);
          $each_member_bonus[$i][$j] = $this->count_total_staff_bonus($data_option, $teams[$i]->staff[$j]->id);
        }
        $teams_data[$i] = [
          'team_name' => $teams[$i]->name,
          'total_sales' => array_sum($each_member_total[$i]),
          'total_bonus' => array_sum($each_member_bonus[$i])
        ];
      }
      else {
        $teams_data[$i] = [
          'team_name' => $teams[$i]->name,
          'total_sales' => 0,
          'total_bonus' => 0
        ];
      }
    }

    // CALCULATE BONUS FOR STAFF LV2
    $staff2_bonus_percentage = 1/100;
    $staff2_bonus_expense = $invoices_total * $staff2_bonus_percentage;

    return response([
      'current_month' => $current_month,
      'current_year' => $current_year,
      'invoices' => $invoices_with_pagination,
      'total_sales' => $invoices_total,
      'staff1_bonus_expense' => $staff_bonus_expense,
      'staff2_bonus_expense' => $staff2_bonus_expense,
      'teams' => $teams_data
    ], 200);
  }

  public function sales_fetch_invoices_for_printing($data_option) {
    $invoices = Invoice::$data_option()->with('staff.working_team')->get();
    return response($invoices, 200);
  }

  public function sales_delete_invoice($invoice_id)
  {
    Invoice::find($invoice_id)->delete();
    return response([], 204);
  }

  private function count_invoices_total($range, $staff_id)
  {
    $invoices = Invoice::$range()->where('staff_id', $staff_id)->get();

    if (count($invoices) > 0) {
      for ($i=0; $i<count($invoices); $i++) {
        $totals_array[] = $invoices[$i]->total;
      }
      $invoices_total = array_sum($totals_array);
    }
    else {
      $invoices_total = 0;
    }

    return $invoices_total;
  }

  private function count_total_staff_bonus($range, $staff_id)
  {
    $invoices = Invoice::$range()->where('staff_id', $staff_id)->get();

    if (count($invoices) > 0) {
      for ($i=0; $i<count($invoices); $i++) {
        $bonuses_array[] = $invoices[$i]->staff_bonus;
      }
      $total_bonus = array_sum($bonuses_array);
    }
    else {
      $total_bonus = 0;
    }

    return $total_bonus;
  }
}

<?php

/* ---- START: PUBLIC PAGES ROUTES ---- */

// Route::get('test_pusher_client', 'PagesController@test_pusher_client');
// Route::get('test_pusher_server', 'PagesController@test_pusher_server');

Route::get('get_server_time', 'PagesController@fetch_server_time');
Route::get('get_business_days', 'PagesController@fetch_business_days');
Route::get('get_today_schedule', 'PagesController@fetch_today_schedule');
Route::get('location', 'PagesController@location');
Route::get('about_us', 'PagesController@about');

Route::get('products_gallery', 'PagesController@products_gallery');
Route::get('products_gallery/photos/all', 'PagesController@fetch_all_categories_with_photos'); // VUE

Route::group(['middleware' => ['web']], function() {
  Route::get( '/', 'PagesController@main');

  Route::get( 'email_us', 'PagesController@email_form');
  Route::post('email_us', 'PagesController@email_send');

  Route::post('track_order', 'PagesController@track_order');
});
/* ---- END: PUBLIC PAGES ROUTES ---- */

/* ---- START: MYAUTH ROUTES ---- */
Route::group(['middleware' => ['web']], function() {
  Route::get( 'register/test_flash', 'MyAuthController@test_flash');
  Route::get( 'register', 'MyAuthController@register_form');
  Route::post('register', 'MyAuthController@register_post');
  
  Route::get( 'login', 'MyAuthController@login_form');
  Route::post('login', 'MyAuthController@login');
  
  Route::get( 'logout', 'MyAuthController@logout');
  
  Route::get( 'reset_password', 'MyAuthController@reset_password_form');
  
  Route::get( 'verify/{user_verification_token}.{user_id}', 'MyAuthController@register_verify_email');
  Route::get( 'verify_as_staff/{user_id}.{user_firstname}.{user_lastname}', 'MyAuthController@verify_as_staff');
});
/* ---- END: MYAUTH ROUTES ---- */

/* ---- START: WORKSHOP ROUTES ---- */
Route::group(['prefix' => 'workshop'], function() {
  Route::group(['middleware' => 'web'], function() {

    Route::get('order_list', 'WorkshopController@order_list');
    Route::get('order_list/order_list/get_orders', 'WorkshopController@fetch_orders'); // VUE

    Route::get('order_in', 'WorkshopController@order_in');
    Route::get('order_in/get_today_date', 'WorkshopController@fetch_today_date'); // VUE
    Route::get('order_in/get_users', 'WorkshopController@get_users'); // VUE
    Route::get('order_in/get_staffs', 'WorkshopController@get_staffs'); // VUE
    Route::get('order_in/get_items', 'WorkshopController@get_items'); // VUE
    Route::post('order_in/register_new_user', 'WorkshopController@register_new_user'); // VUE
    Route::post('order_in/add_order', 'WorkshopController@add_order'); // VUE
  });
});
/* ---- END: WORKSHOP ROUTES ---- */

/* ---- START: USER PAGES ROUTES ---- */
Route::group(['prefix' => 'user'], function() {
  Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('{user_id}', 'UserPagesController@index');
    Route::get('{user_id}/main', 'UserPagesController@main');
    Route::get('{user_id}/profile', 'UserPagesController@my_profile');
    Route::get('{user_id}/profile/fetchProfile', 'UserPagesController@fetchProfile');
    Route::get('{user_id}/review', 'UserPagesController@review');
    Route::post('{user_id}/main/submitReview', 'UserPagesController@post_review'); // VUE
  });
});
/* ---- END: USER PAGES ROUTES ---- */

/* ---- START: STAFF PAGES ROUTES ---- */
Route::group(['prefix' => 'staff'], function() {
  Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get( '/', 'StaffPagesController@index');

    Route::get( 'order_in', 'WorkshopController@order_in');
    
    Route::get( 'dashboard/{staff_id}', 'StaffPagesController@dashboard');
    Route::get( 'dashboard/{staff_id}/get_info_staff_data', 'StaffPagesController@dashboard_fetch_info_staff_data');
    Route::get( 'dashboard/{staff_id}/get_this_month_sales_data', 'StaffPagesController@dashboard_fetch_this_month_sales_data');
    Route::get( 'dashboard/{staff_id}/order_list/get_orders', 'StaffPagesController@order_list_fetch_orders');
    Route::post('dashboard/{staff_id}/order_list/update/{url_name}', 'StaffPagesController@order_list_update');
    
    Route::get( 'profile/{staff_id}', 'StaffPagesController@profile');
    Route::get( 'profile/getStaffData/{staff_id}', function($staff_id) {
      $staff = App\User::findOrFail($staff_id);
      return $staff;
    });

    Route::get('price_list', 'StaffPagesController@price_list');

    Route::get('bonus_table', 'StaffPagesController@bonus_table');

    Route::get( 'pos/users', 'StaffPagesController@pos_get_users');
    Route::get( 'pos/categories', 'StaffPagesController@pos_get_categories');
    Route::get( 'pos/{staff_id}', 'StaffPagesController@pos_system');
    Route::post('pos/{staff_id}/invoice/store_invoice', 'StaffPagesController@pos_invoice_store_invoice'); // VUE
    Route::post('pos/{staff_id}/invoice/store_transaction/{invoice_id}', 'StaffPagesController@pos_invoice_store_transaction'); // VUE
    Route::post('pos/invoice/cancel', 'StaffPagesController@pos_cancel_invoice'); // VUE
    Route::get( 'pos/category/{category_id}', 'StaffPagesController@pos_get_option_category_name');
    Route::get( 'pos/products/{category_id}', 'StaffPagesController@pos_get_products');
    Route::get( 'pos/getTeammate/{staff_id}', 'StaffPagesController@pos_fetch_team_mate');
    Route::get( 'pos/getStaffAndWorkingTeamData/{staff_id}', function($staff_id) {
      $staff = App\User::findOrFail($staff_id);
      $staff->working_team;
      return $staff;
    });
    Route::get('pos/search/user/{search_key}', function($search_key) {
    });
    Route::get('pos/search/product_category/{search_key}', function($search_key) {
      $categories = App\ProductCategory::where('name', $search_key)->get();
      return $categories;
    });

    Route::get('sales/{staff_id}', 'StaffPagesController@sales');
    Route::get('sales/getStaffAndWorkingTeamData/{staff_id}', function($staff_id) {
      $staff = App\User::findOrFail($staff_id);
      $staff->working_team;
      return $staff;
    });
    Route::get('sales/getWorkingTeamData/{staff_id}', function($staff_id) {
      $staff = App\User::findOrFail($staff_id);
      $working_team_id = $staff->working_team[0]->id;
      $working_team = App\WorkingTeam::findOrFail($working_team_id);
      return [$working_team->name, $working_team->staff];
    });
    Route::get('sales/get_sales/{staff_id}/{data_option}', 'StaffPagesController@get_sales');
    Route::delete('sales/invoice/delete/{invoice_id}', 'StaffPagesController@sales_delete_invoice');
    Route::get('sales/get_user_name/{user_id}', function($user_id) {
      $user = App\User::findOrFail($user_id);
      return $user->firstname.' '.$user->lastname;
    });

    // VUE ROUTES
    Route::get('getItemData/{product_id}', function($product_id) {
      $product = App\Product::findOrFail($product_id);
      return $product->name;
    });
    Route::get('bonus_table/categories', function() {
      $categories = App\ProductCategory::with('products')->get();
      return $categories;
    });

    Route::get('{staff_id}/sales/invoice_details/{invoice_id}', function($staff_id, $invoice_id) {
      return App\Transaction::where('id', $invoice_id)->get();
    });
  });
});
/* ---- END: STAFF PAGES ROUTES ---- */

/* ---- START: ADMIN PAGES ROUTES ---- */
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth']], function() {

  Route::get('/', 'AdminPagesController@index');
  Route::get('dashboard/get_daily_sales_chart_data', 'AdminPagesController@dashboard_fetch_daily_sales_chart_data');
  Route::get('dashboard/get_today_sales_data', 'AdminPagesController@dashboard_fetch_today_sales_data');

  Route::get('users', 'UsersController@index');
  Route::get('users/user', function() {
    $users_regular = App\User::where('user_level', 1)->get();
    return $users_regular;
  });
  Route::get('users/get_working_team', 'AdminPagesController@users_get_working_team'); // VUE
  Route::post('users/update_level', 'AdminPagesController@users_update_level'); // VUE
  Route::post('users/update_team', 'AdminPagesController@users_update_team'); // VUE
  Route::get('users/getAllUsers', function() {
    $all = App\User::with('working_team')->get();
    $total_all = App\User::all()->count();
    $total_users = App\User::User()->count();
    $total_staff = App\User::Staff()->count();
    $total_admin = App\User::Admin()->count();
    $total = [
      'all' => $total_all,
      'users' => $total_users,
      'staff' => $total_staff,
      'admin' => $total_admin
    ];
    return response([$all, $total], 200);
  });
  Route::get('users/getOnlyUsers', function() {
    $users = App\User::User()->get();
    $total = App\User::User()->count();
    return response([$users, $total], 200);
  });
  Route::get('users/getOnlyStaffs', function() {
    $staff = App\User::Staff()->with('working_team')->get();
    $total = App\User::Staff()->count();
    return response([$staff, $total], 200);
  });

  Route::get('holidays', 'AdminPagesController@holidays_form');
  Route::post('holidays', 'AdminPagesController@holiday_store');

  Route::get('working_days', 'AdminPagesController@working_days');
  Route::get('working_days/get_working_days', function() {
    $working_days = App\ShopSchedule::where('day', '<', 7)->get();
    if ($working_days->isEmpty()) {
      return [null, null];
    }
    else {
      foreach ($working_days as $working_day) {
        $taken_days[] = $working_day->day;
      }
      return [$taken_days, $working_days];
    }
  });
  Route::post('working_days/add', 'AdminPagesController@working_days_add'); // VUE
  Route::patch('working_days/update', 'AdminPagesController@working_days_update'); // VUE
  Route::delete('working_days/delete/{day}', function($day) {
    $deleted_working_day = App\ShopSchedule::where('day', $day)->firstOrFail()->delete();
    if ($deleted_working_day) {
      return response([], 200);
    }
    else {
      return response([], 500);
    }
  });

  Route::get('gallery', 'AdminPagesController@manage_gallery');

  Route::get('products/categories', 'AdminPagesController@categories_index'); // DISPLAY UI FOR MANAGING PRODUCT-CATEGORIES
  Route::get('products/categories/all', 'AdminPagesController@categories_all'); // VUE: getCategories()
  Route::post('products/categories/add', 'AdminPagesController@categories_add'); // VUE: addNewCategory()
  Route::get('products/categories/{id}', 'AdminPagesController@categories_show'); // VUE: editCategory()
  Route::patch('products/categories/{id}', 'AdminPagesController@categories_update'); // VUE: updateCategory()
  Route::delete('products/categories/{id}/delete', 'AdminPagesController@categories_delete'); // VUE: deleteCategory()
  Route::get('products/category-{id}/manage', 'AdminPagesController@products_index'); // DISPLAY UI FOR MANAGING PRODUCTS
  Route::get('products/categories/{id}/manage/all', 'AdminPagesController@products_all'); // VUE: getProducts()
  Route::post('products/categories/{id}/manage/add', 'AdminPagesController@products_add'); // VUE: addNewProduct()
  Route::get('products/categories/{category_id}/manage/show/{product_id}', 'AdminPagesController@products_show'); // VUE: showProduct()
  Route::patch('products/categories/{category_id}/manage/update/{product_id}', 'AdminPagesController@products_update'); //VUE: updateProduct()
  Route::delete('products/categories/{category_id}/manage/delete/{product_id}', 'AdminPagesController@products_delete'); // VUE:: deleteProduct()

  // BELOW ARE ROUTES FOR SALES
  Route::get('sales', 'AdminPagesController@sales');
  Route::get('sales/get_sales/{data_option}', 'AdminPagesController@sales_get_sales');
  Route::get('sales/get_invoices_for_printing/{data_option}', 'AdminPagesController@sales_fetch_invoices_for_printing');
  Route::delete('sales/delete_invoice/{invoice_id}', 'AdminPagesController@sales_delete_invoice');
});
/* ---- END: ADMIN PAGES ROUTES ---- */

/* ---- START: ADMIN PAGES ROUTES (WITH PARAMETERS) ---- */
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth']], function() {
  Route::get('holiday/{holiday_id}/edit', 'AdminPagesController@holiday_edit');
  Route::patch('holiday/{holiday_id}/update', 'AdminPagesController@holiday_update');
  Route::get('holiday/{holiday_id}/delete', 'AdminPagesController@holiday_delete');
  Route::get('working_hours/{id}/delete', 'AdminPagesController@working_hours_delete');
  Route::get('working_hours/{id}/edit', 'AdminPagesController@working_hours_edit');
  Route::get('gallery/{category_id}/manage', 'AdminPagesController@gallery_show');
  Route::get('gallery/{category_id}/delete', 'AdminPagesController@gallery_delete_category');
  Route::post('gallery/{category_id}/manage/add_photos', 'AdminPagesController@gallery_add_photos');
  Route::get('gallery/{category_id}/delete_photo/{photo_id}', 'AdminPagesController@gallery_delete_photo');
  Route::get('products/categories/{id}', 'AdminPagesController@categories_show'); // VUE: editCategory()
  Route::patch('products/categories/{id}', 'AdminPagesController@categories_update'); // VUE: updateCategory()
  Route::delete('products/categories/{id}/delete', 'AdminPagesController@categories_delete'); // VUE: deleteCategory()
  Route::get('products/categories/{id}/manage', 'AdminPagesController@products_index'); // DISPLAY UI FOR MANAGING PRODUCTS
  Route::get('products/categories/{id}/manage/all', 'AdminPagesController@products_all'); // VUE: getProducts()
  Route::post('products/categories/{id}/manage/add', 'AdminPagesController@products_add'); // VUE: addNewProduct()
  Route::get('products/categories/{category_id}/manage/{product_id}/show', 'AdminPagesController@products_show'); // VUE: showProduct()
  Route::patch('products/categories/{category_id}/manage/{product_id}/update', 'AdminPagesController@products_update'); //VUE: updateProduct()
  Route::delete('products/categories/{category_id}/manage/{product_id}/delete', 'AdminPagesController@products_delete'); // VUE:: deleteProduct()
  Route::post('products/categories/{category_id}/manage/save_history/{product_id}', 'AdminPagesController@products_save_history');
});
/* ---- END: ADMIN PAGES ROUTES (WITH PARAMETERS) ---- */

/* ---- START: VUE ROUTES ---- */
Route::get('register/titles', function() {
  $titles = App\UserTitle::all();
  return $titles;
});
Route::get('register/countries', function() {
  $countries = App\Country::all();
  return $countries;
});
Route::get('register/{country_id}/states', function($country_id) {
  $selected_country = App\Country::findOrFail($country_id);
  $states = $selected_country->state;
  return $states;
});
Route::get('register/contact_types', function() {
  return App\ContactType::all();
});
Route::get('price_list/all', function() {
  $categories = App\ProductCategory::with('products')->get();
  return $categories;
});
/* ---- END: VUE ROUTES ---- */

<?php

use App\Http\Controllers\admin\customer_controller;
use App\Http\Controllers\admin\EmailController;
use App\Http\Controllers\admin\loan_controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\login_controller;
use App\Http\Controllers\admin\profile_controller;
use App\Http\Controllers\admin\setting_controller;
use App\Http\Controllers\admin\statement_controller;
use App\Http\Controllers\customer\customer_dashboard_controller;
use App\Http\Controllers\customer\customer_loan_controller;
use App\Http\Controllers\customer\customer_login_controller;
use App\Http\Controllers\customer\customer_setting_controller;
use App\Http\Controllers\customer\customer_statement_controller;
use App\Http\Middleware\admin_login;
use App\Http\Middleware\customer_login;
use App\Http\Middleware\send_email;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('index');
});

Route::get('/admin', function () {
    return view('admin.login');
});

Route::post('/login_admin', [login_controller::class, 'admin_login']);

//route for login varification
Route::get('/logout_admin', [login_controller::class, 'logout_admin']);


Route::get('/dashboard', [login_controller::class, 'dashboard'])->Middleware(admin_login::class);

Route::get('/collection', function () {
    return view('admin.pages.collection');
})->Middleware(admin_login::class);


Route::get("/monthly_expire", [loan_controller::class, 'monthly_expire'])->Middleware(admin_login::class);


Route::post("/submit_collection", [statement_controller::class, 'submit_cutomers_collection'])->Middleware(admin_login::class);

Route::get("/get_customer_data/{id}", [statement_controller::class, 'get_customer_info'])->Middleware(admin_login::class);


Route::get('/send_email', [EmailController::class, 'sendEmail'])->Middleware(send_email::class);

Route::get('/new_cust', [customer_controller::class, 'open_new_account'])->Middleware(admin_login::class);

Route::post('/open_account', [customer_controller::class, 'add_new_customer'])->Middleware(admin_login::class);

Route::get('/customers', [customer_controller::class, 'get_customers'])->Middleware(admin_login::class);


Route::get("/profile/{id}", [profile_controller::class, 'get_customers_data'])->Middleware(admin_login::class);

Route::post("/customer_profile", [profile_controller::class, 'get_profile_data'])->Middleware(admin_login::class);


Route::get("/calculate_penalty/{id}", [statement_controller::class, 'calculate_penalty'])->Middleware(admin_login::class);

Route::get("/collect_penalty/{id}", [statement_controller::class, 'collect_penalty'])->Middleware(admin_login::class);

Route::post("/submit_penalty/{id}", [statement_controller::class, 'submit_penalty'])->Middleware(admin_login::class);

Route::get("/monthly_statement/{id}", [statement_controller::class, 'get_monthly_statement_data'])->Middleware(admin_login::class);

Route::get("/statement/{id}", [statement_controller::class, 'get_statement'])->Middleware(admin_login::class);

Route::get("/cancel_transaction/{id}", [statement_controller::class, 'cancel_transaction'])->Middleware(admin_login::class);

Route::get("/give_a_loan/{id}", [loan_controller::class, 'give_a_new_loan'])->Middleware(admin_login::class);

Route::post("/submit_loan/{id}", [loan_controller::class, 'submit_loan_to_customer'])->Middleware(admin_login::class);

Route::get("/current_month_status/{loan_no}/{id}/", [loan_controller::class, 'get_current_month_status'])->Middleware(admin_login::class);

Route::get("/cancel_loan_transaction/{id}", [loan_controller::class, 'cancel_loan_transaction'])->Middleware(admin_login::class);

Route::get("/loan_statement/{loan_no}/{id}/", [loan_controller::class, 'get_loan_statement'])->Middleware(admin_login::class);

Route::get("/monthly_loan_statement/{loan_no}/{id}", [loan_controller::class, 'get_monthly_loan_statement'])->Middleware(admin_login::class);

Route::get("/collect_all_loan/{id}", [loan_controller::class, 'collect_all_loan_amount'])->Middleware(admin_login::class);

Route::get("/calculate_interest/{id}", [loan_controller::class, 'calculate_interest_of_loan'])->Middleware(admin_login::class);

Route::post("/submit_all_loan/{id}", [loan_controller::class, 'submit_all_loan'])->Middleware(admin_login::class);



Route::get('/edit_profile/{id}', [customer_controller::class, 'get_customers_data_for_edit'])->Middleware(admin_login::class);

Route::post('/update_customer_info/{id}', [customer_controller::class, 'update_customer_info'])->Middleware(admin_login::class);


Route::get('/change_pin', function () {
    return view('admin.pages.change_pin');
})->Middleware(admin_login::class);

Route::post('/update_pin', [setting_controller::class, 'update_admin_pin'])->Middleware(admin_login::class);


Route::get('/change_pass', function () {
    return view('admin.pages.change_pass');
})->Middleware(admin_login::class);

Route::post('/update_password', [setting_controller::class, 'update_admin_pass'])->Middleware(admin_login::class);

Route::post('/update_password', [setting_controller::class, 'update_admin_pass'])->Middleware(admin_login::class);




// ------------------------------------------------User---------------------------------------------------------

Route::get('/customer', function () {
    return view('customer.login');
});

Route::post('/customer_login', [customer_login_controller::class, 'customer_login']);

//route for login varification
Route::get('/customer_logout', [customer_login_controller::class, 'customer_logout']);


Route::get('/customers_dashboard', [customer_dashboard_controller::class, 'customer_dashboard'])->Middleware(customer_login::class);


Route::get("/customer_statement/{id}", [customer_statement_controller::class, 'get_customer_bachat_statement'])->Middleware(customer_login::class);

Route::get("/customer_monthly_statement/{id}", [customer_statement_controller::class, 'get_customer_monthly_statement_data'])->Middleware(customer_login::class);


Route::get("/customer_loan_statement/{loan_no}/{id}/", [customer_loan_controller::class, 'get_customer_Loan_statement'])->Middleware(customer_login::class);

Route::get("/customer_monthly_loan_statement/{loan_no}/{id}", [customer_loan_controller::class, 'get_customer_monthly_loan_statement'])->Middleware(customer_login::class);


Route::get('/customers_change_pass', [customer_setting_controller::class, 'customers_change_pass'])->Middleware(customer_login::class);

Route::post('/customer_update_password', [customer_setting_controller::class, 'update_customer_pass'])->Middleware(customer_login::class);
// -------------------------------------------------------------------------------------------------------------

// route for clear all cache data
Route::get('/cache_data', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('event:clear');
    Artisan::call('route:clear');
    return "Cache is cleared";
});
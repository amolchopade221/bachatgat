<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class customer_controller extends Controller
{
    public function create_Account_no($cuss_count)
    {
        $add1 = $cuss_count + 1;
        $len = strlen($add1);
        $final_user_acc = null;
        if ($len == 1 ||  strlen($add1) == 1) {
            $final_user_acc = 'JGTP0000' . $add1;
        } else if ($len == 2 && strlen($add1) == 2) {
            $final_user_acc = 'JGTP000' . $add1;
        } else if ($len == 3 && strlen($add1) == 3) {
            $final_user_acc = 'JGTP00' . $add1;
        } else if ($len == 4 && strlen($add1) == 4) {
            $final_user_acc = 'JGTP0' . $add1;
        } else if ($len == 5 && strlen($add1) == 5) {
            $final_user_acc = 'JGTP' . $add1;
        }
        return $final_user_acc;
    }

    public function open_new_account(Request $request)
    {
        try {
            $cuss_last_id = DB::select("SELECT id FROM customers ORDER BY id DESC LIMIT ?", [1]);
            $customer_count = DB::select("SELECT * FROM customers");

            if (count($cuss_last_id) == 0) {
                $cuss_id = 0;
                $final_user_account_no = $this->create_Account_no($cuss_id);

                $request->session()->put('customer_count', 1);

                return view('admin.pages.new_cust', array('account_no' => $final_user_account_no, 'success_code' => 200));
            } else {
                foreach ($cuss_last_id as $count) {
                    $cuss_last_id = $count->id;
                }

                $final_user_account_no = $this->create_Account_no($cuss_last_id);

                $cuss_count = count($customer_count);
                $request->session()->put('customer_count', $cuss_count + 1);

                return view('admin.pages.new_cust', array('account_no' => $final_user_account_no, 'success_code' => 200));
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
    // FUnction For Adding New Customer
    function add_new_customer(Request $request)
    {
        // $open_date = $request->input('date');

        // echo $open_date . "\n";

        // echo "start date is" . $open_date;

        // $date = new DateTime($open_date);
        // $date->modify('+1 month');
        // $date->modify('-1 day');
        // $expry_date = $date->format('d-m-Y');

        // echo "expiry date is" . $expry_date . "\n";

        // $date = new DateTime($expry_date);
        // $date->modify('+1 day');
        // $expre = $date->format('d-m-Y');

        // echo "new month start date is" . $expre . "\n";

        // $today = Date('d-m-Y');

        // echo "\n" . "tody date is" . $today;

        // $dateTimestamp1 = strtotime($open_date);
        // $dateTimestamp2 = strtotime($today);
        // if ($dateTimestamp1 <= $dateTimestamp2) {
        //     echo "done";
        // } else {
        //     echo "error";
        // }

        $acount_no = $request->input('account_no');
        $full_name = $request->input('full_name');
        $shop_name = $request->input('shop_name');
        $mobile = $request->input('mobile');
        $aadhaar = $request->input('aadhaar');
        $pan = $request->input('pan');
        $email = $request->input('email');
        $amount = $request->input('bachat_amount');
        $open_date = $request->input('date');

        $date = new DateTime($open_date);
        $date->modify('+5 years');
        $expre_date = $date->format('d-m-Y');

        $address = $request->input('address');
        $pass = $request->input('pass');
        try {
            $customer_data = DB::select('SELECT * FROM customers WHERE `acc_no`=?', [$acount_no]);
            if (count($customer_data) > 0) {
                return back()->with('message', 'Your Account Open Successfully.....');
            }
            $profile = time() . '.' . $request->photo->getClientOriginalExtension();
            $img = $request->photo->move(('profile'), $profile);
            if ($img) {
                $customer_data = array(
                    'acc_no' => $acount_no, 'full_name' => $full_name,  'shop_name' => $shop_name, 'per_month_bachat' => $amount,
                    'mobile_no' => $mobile, 'email' => $email, 'aadhaar' => $aadhaar, 'pan' => $pan,
                    'aadhaar' => $aadhaar, 'address' => $address, 'profile' => $profile,
                    'acc_open_date' => $open_date, 'acc_expire_date' => $expre_date, 'pass' => $pass
                );
                $check = DB::table('customers')->insert($customer_data);
                if ($check) {
                    return back()->with('message', 'Your Account Open Successfully.....');
                } else {
                    return back()->with('error', 'Something Went Wrong.....');
                }
            } else {
                return back()->with('error', 'Image Storing Problem.....');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    // Function For Display Customer List
    public function get_customers(Request $request)
    {
        $customer_list = DB::select('SELECT * FROM customers');
        if (count($customer_list) == 0) {
            return view('admin.pages.customers', array('data' => 0));
        } else {
            return view('admin.pages.customers', array('customer_list' => $customer_list, 'data' => 1));
        }
    }

    // Function For Display Customer data
    public function get_customers_data_for_edit($id)
    {
        $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$id]);
        if (count($customer_data) == 0) {
            return view('admin.pages.edit_profile', array('data' => 0));
        } else {
            return view('admin.pages.edit_profile', array('customer_data' => $customer_data, 'data' => 1));
        }
    }

    // FUnction For Update Customer Data
    function update_customer_info(Request $request, $id)
    {
        $full_name = $request->input('full_name');
        $shop_name = $request->input('shop_name');
        $mobile = $request->input('mobile');
        $email = $request->input('email');
        $aadhaar = $request->input('aadhaar');
        $pan = $request->input('pan');

        $address = $request->input('address');
        $pass = $request->input('pass');

        try {
            $check = DB::update(
                'UPDATE `customers` SET `full_name` =?,  `shop_name` = ?,
                `mobile_no` =?, `email` =?, `aadhaar` =?, `pan` =?,`address` =?, `pass` =? WHERE id =?',
                [$full_name, $shop_name, $mobile, $email, $aadhaar, $pan, $address, $pass, $id]
            );

            if ($check) {
                return back()->with('message', 'Information Update Successfully.....');
            } else {
                return back()->with('error', 'Something Went Wrong.....');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
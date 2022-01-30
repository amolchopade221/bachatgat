<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class customer_statement_controller extends Controller
{
    // Function For Statement data
    public function get_customer_bachat_statement(Request $request, $id)
    {
        $acc_no = $request->session()->get('acc_no');

        $loan_data = DB::select('SELECT * FROM `loan` WHERE `account_no`=? AND `status`=?', [$acc_no, 0]);
        $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$id]);
        $statement_data = DB::select('SELECT * FROM `statement` WHERE `customer_id`=?', [$id]);
        if ((count($customer_data) == 0 || ($statement_data) == 0)) {
            return back()->with('error', 'Data Not Found.....');
        } else {
            return view('customer.pages.statement', array('customer_data' => $customer_data, 'sr' => 0, 'current_loan_data' => $loan_data, 'statement_data' => $statement_data, 'balance' => 0));
        }
    }

    // Function For Monthly Statement data
    public function get_customer_monthly_statement_data(Request $request, $id)
    {
        $acc_no = $request->session()->get('acc_no');

        $loan_data = DB::select('SELECT * FROM `loan` WHERE `account_no`=? AND `status`=?', [$acc_no, 0]);
        $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$id]);
        $monthly_statement_data = DB::select('SELECT * FROM `bachat_monthly` WHERE `customer_id`=?', [$id]);
        if ((count($customer_data) == 0 || ($monthly_statement_data) == 0)) {
            return back()->with('error', 'Data Not Found.....');
        } else {
            return view('customer.pages.monthly_bachat_statement', array('customer_data' => $customer_data, 'sr' => 0, 'current_loan_data' => $loan_data, 'monthly_statement_data' => $monthly_statement_data));
        }
    }
}

<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class customer_loan_controller extends Controller
{
    // Function For Loan Statement data
    public function get_customer_Loan_statement(Request $request, $loan_no, $customer_id)
    {

        $acc_no = $request->session()->get('acc_no');

        $loan_data = DB::select('SELECT * FROM `loan` WHERE `account_no`=? AND `status`=?', [$acc_no, 0]);
        $all_loan_data = DB::select('SELECT * FROM loan WHERE `loan_no`=? AND `customer_id`=?', [$loan_no,$customer_id]);
        if (count($all_loan_data) != 0) {
            foreach ($all_loan_data as $all_loan) {
                $pending_loan = $all_loan->amount;
                $interest = $all_loan->interest;
                $status = $all_loan->status;
            }
            if ($status == 1) {
                $pending_loan = $pending_loan + $interest;
            }
            $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$customer_id]);
            $loan_statement_data = DB::select('SELECT * FROM `loan_statement` WHERE `loan_no`=? AND `customer_id`=?', [$loan_no, $customer_id]);
            // print_r($loan_statement_data);
            // die();
            if (count($customer_data) != 0) {
                return view('customer.pages.loan_statement', array('customer_data' => $customer_data, 'sr' => 0, 'current_loan_data' => $loan_data, 'all_loan_data' => $all_loan_data, 'loan_statement_data' => $loan_statement_data, 'pending' => $pending_loan));
            } else {
                return back()->with('error', 'Data Not Found.....');
            }
        } else {
            return back()->with('error', 'Data Not Found.....');
        }
    }

    // Function For Monthly Loan Statement data
    public function get_customer_monthly_loan_statement(Request $request, $loan_no, $customer_id)
    {
        $acc_no = $request->session()->get('acc_no');

        $loan_data = DB::select('SELECT * FROM `loan` WHERE `account_no`=? AND `status`=?', [$acc_no, 0]);

        $all_loan_data = DB::select('SELECT * FROM loan WHERE `loan_no`=? AND `customer_id`=?', [$loan_no,$customer_id]);
        if (count($all_loan_data) != 0) {
            foreach ($all_loan_data as $all_loan) {
                $status = $all_loan->status;
            }
            $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$customer_id]);
            $monthly_loan_statement_data = DB::select('SELECT * FROM `loan_monthly_status` WHERE `loan_no`=? AND `customer_id`=?', [$loan_no, $customer_id]);

            if (count($customer_data) == 0) {
                return back()->with('error', 'Data Not Found.....');
            } else {
                return view('customer.pages.monthly_loan_statement', array('status' => $status, 'customer_data' => $customer_data, 'sr' => 0, 'current_loan_data' => $loan_data, 'all_loan_data' => $all_loan_data, 'monthly_loan_statement' => $monthly_loan_statement_data));
            }
        } else {
            return back()->with('error', 'Data Not Found.....');
        }
    }
}
<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class loan_controller extends Controller
{

    // Function For Loan Form
    public function give_a_new_loan($id)
    {
        $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$id]);
        $loan_data = DB::select('SELECT * FROM loan WHERE `customer_id`=?', [$id]);
        $loan_no = count($loan_data);
        if (count($customer_data) == 0) {
            return back()->with('error', 'Data Not Found.....');
        } else {
            if ($loan_no == 0) {
                $loan_no = 1;
            } else {
                $loan_no++;
            }
            return view('admin.pages.give_a_loan', array('customer_data' => $customer_data, 'loan_no' => $loan_no));
        }
    }

    // Function For Submit Loan To Customer
    public function submit_loan_to_customer(Request $request, $id)
    {
        $loan_no = $request->input('loan_no');
        $start_date = $request->input('start_date');
        $loan_amount = $request->input('loan_amount');
        $shares_amount = $request->input('shares_amount');
        $con_pin = $request->input('con_pin');

        $date = new DateTime($start_date);
        $date->modify('+10 month');
        $loan_end_date = $date->format('d-m-Y');

        $date = new DateTime($start_date);
        $date->modify('+1 month');
        $new_month_start_date = $date->format('d-m-Y');

        $date->modify('-1 day');
        $end_date = $date->format('d-m-Y');

        $today = Date('d-m-Y');

        $date = new DateTime($today);
        $date->modify('-30 days');
        $check_date = $date->format('d-m-Y');

        $dateTimestamp1 = strtotime($start_date);
        $dateTimestamp2 = strtotime($check_date);
        $dateTimestamp4 = strtotime($today);
        try {
            if ($dateTimestamp1 > $dateTimestamp2) {
                if ($dateTimestamp1 <= $dateTimestamp4) {
                    $admin_data = DB::select('SELECT * FROM `admin`');
                    $loan_data = DB::select('SELECT * FROM loan WHERE `customer_id`=? AND `status`=?', [$id, 0]);
                    if (count($admin_data) != 0) {
                        foreach ($admin_data as $admin_data) {
                            $pin = $admin_data->pin;
                        }
                        if ($con_pin == $pin) {
                            if (count($loan_data) == 0) {
                                $cust_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$id]);
                                if (count($cust_data) != 0) {
                                    foreach ($cust_data as $cust_data) {
                                        $acc_no = $cust_data->acc_no;
                                    }
                                    $monthly_emi = ($loan_amount/10);

                                    $loan_data = array(
                                        'loan_no' => $loan_no, 'customer_id' => $id, 'account_no' => $acc_no, 'amount' => $loan_amount, 'shares_amount' => $shares_amount, 'pending_loan' => $loan_amount, 'monthly_emi'=> $monthly_emi,
                                        'start_date' => $start_date, 'end_date' => $loan_end_date
                                    );
                                    $check = DB::table('loan')->insert($loan_data);
                                    if ($check) {
                                        $monthly_loan_data = array(
                                            'loan_no' => $loan_no, 'customer_id' => $id, 'account_no' => $acc_no, 'loan_amount' => $loan_amount, 'monthly_pending_loan' => $loan_amount,
                                            'pending_loan' => $loan_amount, 'start_date' => $start_date, 'end_date' => $end_date, 'new_month_start_date' => $new_month_start_date
                                        );
                                        $check = DB::table('loan_monthly_status')->insert($monthly_loan_data);
                                        if ($check) {
                                            return back()->with('message', 'Loan Amount Submitted Sucessfully.....');
                                        } else {
                                            return back()->with('error', 'Something Went Wrong.....');
                                        }
                                    } else {
                                        return back()->with('error', 'Something Went Wrong.....');
                                    }
                                } else {
                                    return back()->with('error', 'Customer Not Found.....');
                                }
                            } else {
                                return back()->with('error', 'Allready Loan Is Active.....');
                            }
                        } else {
                            return back()->with('error', 'Pin Not Match.....');
                        }
                    } else {
                        return back()->with('error', 'Admin Data Not Found.....');
                    }
                } else {
                    return back()->with('error', 'Loan Start Date Should Not Greater Than Todays Date.....');
                }
            } else {
                return back()->with('error', 'Loan Start Date Should Near Of Todays Date.....');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    // Function For show Pending Loan
    public function show_pending_loans()
    {
        $loan_data = DB::select('SELECT loan.customer_id, loan.account_no, loan.amount, loan.pending_loan, loan.monthly_emi, loan.completed_months, customers.full_name, customers.is_active FROM loan INNER JOIN customers ON loan.customer_id = customers.id');
        return view('admin.pages.pending_loan', array('loan_data' => $loan_data));
    }

    // Function For collect all loan amount
    public function collect_all_loan_amount($id)
    {
        $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$id]);
        $loan_data = DB::select('SELECT * FROM loan WHERE `customer_id`=? AND `status`=?', [$id, 0]);
        if ((count($loan_data) != 0) && (count($customer_data) != 0)) {
            return view('admin.pages.collect_all_loan', array('customer_data' => $customer_data, 'loan_data' => $loan_data));

            // foreach ($loan_data as $curr_loan_data) {
            //     $loan_no = $curr_loan_data->loan_no;
            //     $pending_loan = $curr_loan_data->pending_loan;
            //     $interest = $curr_loan_data->interest;
            // }
            // $monthly_loan_data = DB::select('SELECT * FROM `loan_monthly_status` WHERE `customer_id`=? AND `loan_no`= ? AND `is_expire`=?', [$id, $loan_no, 0]);
            // if (count($monthly_loan_data) != 0) {
            //     foreach ($monthly_loan_data as $curr_month_loan_data) {
            //         $pending_month_id = $curr_month_loan_data->id;
            //         $interest_is_calculate = $curr_month_loan_data->interest_is_calculate;
            //         $pending_loan = $curr_month_loan_data->pending_loan;
            //     }
            //     $total = $pending_loan + $interest;
            //     return view('admin.pages.collect_all_loan', array('customer_data' => $customer_data, 'pending_month_id' => $pending_month_id, 'interest_is_calculate' => $interest_is_calculate,  'pending' => $pending_loan, 'interest' => $interest, 'total' => $total));
            // } else {
            //     return back()->with('error', 'Data Not Found.....');
            // }
        } else {
            return back()->with('error', 'Data Not Found.....');
        }
    }

    public function calculate_days($start_date,$today){
        $date1_ts = strtotime($start_date);
        $date2_ts = strtotime($today);
        $diff = $date2_ts - $date1_ts;
        return round($diff / 86400);
    }

    public function calculate_interest_of_loan($loan_id)
    {
        $loan_data = DB::select('SELECT * FROM `loan` WHERE `id`=?', [$loan_id]);
        if (count($loan_data) != 0) {
            foreach ($loan_data as $loan_data) {
                $customer_id = $loan_data->customer_id;
                $loan_no = $loan_data->loan_no;
                $total_interest = $loan_data->interest;
            }
            $all_months_loan_status = DB::select('SELECT * FROM loan_monthly_status WHERE `customer_id`=? AND `loan_no`=?', [$customer_id, $loan_no]);
            if (count($all_months_loan_status) != 0) {
                foreach ($all_months_loan_status as $months_loan_data) {
                    $monthly_status_loan_id = $months_loan_data->id;
                    $monthly_pending_loan = $months_loan_data->monthly_pending_loan;
                    $start_date = $months_loan_data->start_date;

                    if (($months_loan_data->is_expire) == 1) {
                        $interest = (($monthly_pending_loan / 100) * 2);
                        $check = DB::update('UPDATE `loan_monthly_status` SET `interest`= ?, `interest_is_calculate`= ? WHERE id = ?', [$interest, 1, $monthly_status_loan_id]);
                        if ($check) {
                            $total_interest = $total_interest + $interest;
                        }
                    }else{
                        $interest = (($monthly_pending_loan / 100) * 2);
                        $today = Date('d-m-Y');
                        $total_days = $this->calculate_days($start_date,$today);
                        $interest = (($interest/30) * $total_days);
                        $check = DB::update('UPDATE `loan_monthly_status` SET `interest`= ?, `interest_is_calculate`= ? WHERE id = ?', [$interest, 1, $monthly_status_loan_id]);
                        if ($check) {
                            $total_interest = $total_interest + $interest;
                        }
                    }
                }
                DB::update('UPDATE `loan` SET `interest`= ?, `is_interest_calculate`=? WHERE id = ?', [$total_interest, 1, $loan_id]);
                return back()->with('message', 'Interest Calculeted Successfully.....');
            } else {
                return back()->with('error', 'Data Not Found.....');
            }
        } else {
            return back()->with('error', 'Data Not Found.....');
        }
    }

    // Function For calculate interest of loan
    public function submit_all_loan(Request $request, $id)
    {
        $con_pin = $request->input('con_pin');
        $total = $request->input('total_amount');

        $admin_data = DB::select('SELECT * FROM `admin`');
        if (count($admin_data) != 0) {
            foreach ($admin_data as $admin_data) {
                $pin = $admin_data->pin;
            }
            if ($con_pin == $pin) {
                $monthly_loan_data = DB::select('SELECT * FROM `loan_monthly_status` WHERE `id`=?', [$id]);
                if (count($monthly_loan_data) != 0) {
                    foreach ($monthly_loan_data as $curr_month_loan_data) {
                        $customer_id = $curr_month_loan_data->customer_id;
                        $account_no = $curr_month_loan_data->account_no;
                        $loan_no = $curr_month_loan_data->loan_no;
                        $pending_loan = $curr_month_loan_data->pending_loan;
                        $amount_of_loan_paid_off = $curr_month_loan_data->amount_of_loan_paid_off;

                        $new_amount_of_loan_paid_off = $amount_of_loan_paid_off + $pending_loan;

                        $customer_data = DB::select('SELECT * FROM `customers` WHERE `id`=?', [$customer_id]);
                        $curr_loan_data = DB::select('SELECT * FROM `loan` WHERE `customer_id`=? AND `loan_no`= ? AND `status`=?', [$customer_id, $loan_no, 0]);

                        if ((count($curr_loan_data) != 0) && (count($customer_data) != 0)) {
                            $check = DB::update('UPDATE `loan_monthly_status` SET `pending_loan`= ?,`amount_of_loan_paid_off`= ?,`is_expire`= ? WHERE id = ?', [0, $new_amount_of_loan_paid_off, 1, $id]);
                            if ($check) {
                                foreach ($curr_loan_data as $curr_loan_data) {
                                    $loan_id = $curr_loan_data->id;
                                    $interest = $curr_loan_data->interest;
                                }
                                $check = DB::update('UPDATE `loan` SET `pending_loan`=?,`status`=? WHERE id = ?', [0, 1, $loan_id]);
                                if ($check) {
                                    $date = date("d-m-Y");
                                    $time = Date("h:i:s");
                                    $amount = $pending_loan + $interest;
                                    $loan_statement = array(
                                        'loan_no' => $loan_no, 'customer_id' => $customer_id,
                                        'month_id' => $id, 'account_no' => $account_no, 'amount' => $amount, 'date' => $date, 'time' => $time,
                                    );
                                    $check = DB::table('loan_statement')->insert($loan_statement);
                                    if ($check) {
                                        $request->session()->put('message', 'Loan Amount Collected Successfully.....');
                                        return view('admin.pages.collect_all_loan', array('customer_data' => $customer_data, 'pending_month_id' =>  $id, 'interest_is_calculate' => 1,  'pending' => 0, 'interest' => 0, 'total' => 0));
                                    } else {
                                        return back()->with('error', 'Something Went Wrong.....');
                                    }
                                } else {
                                    return back()->with('error', 'Something Went Wrong.....');
                                }
                            } else {
                                return back()->with('error', 'Something Went Wrong.....');
                            }
                        } else {
                            $request->session()->put('message', 'Allready Loan Is Collected.....');
                            return view('admin.pages.collect_all_loan', array('customer_data' => $customer_data, 'pending_month_id' =>  $id, 'interest_is_calculate' => 1,  'pending' => 0, 'interest' => 0, 'total' => 0));
                        }
                    }
                } else {
                    return back()->with('error', 'Data Not Found.....');
                }
            } else {
                return back()->with('error', 'Pin Not Match.....');
            }
        } else {
            return back()->with('error', 'Admin Data Not Found.....');
        }
    }
// INSERT INTO `loan`(`id`, `customer_id`, `loan_no`, `account_no`, `amount`, `pending_loan`, `monthly_emi`, `completed_months`, `interest`, `start_date`, `end_date`, `status`) VALUES (1,119,1,"JGTP00119",10000,75000,10000,4,0,11-06-2021,11-06-2022,0)
    // Function For Current Month Loan Loan Statement data

    // Function For cancel transaction
    public function cancel_loan_transaction($id)
    {
        $loan_statement_data = DB::select('SELECT * FROM `loan_statement` WHERE `id`=?', [$id]);
        if (count($loan_statement_data) != 0) {
            foreach ($loan_statement_data as $stat_data) {
                $loan_no = $stat_data->loan_no;
                $customer_id = $stat_data->customer_id;
                $amount = $stat_data->amount;
                $curr_month_id = $stat_data->month_id;
            }
            $curr_month_loan_data = DB::select('SELECT * FROM `loan_monthly_status` WHERE `id`=?', [$curr_month_id]);
            $loan_data = DB::select('SELECT * FROM `loan` WHERE `loan_no`=? AND `customer_id`=?', [$loan_no, $customer_id]);
            if ((count($curr_month_loan_data) != 0)) {
                foreach ($curr_month_loan_data as $curr_month_data) {
                    $month_id = $curr_month_data->id;
                    $prev_amount_of_loan_paid_off = $curr_month_data->amount_of_loan_paid_off;
                    $new_amount_of_loan_paid_off = $prev_amount_of_loan_paid_off - $amount;


                    $check = DB::update('UPDATE `loan_monthly_status` SET `amount_of_loan_paid_off`= ?,`interest`= ?,`interest_is_calculate`= ? WHERE id = ?', [$new_amount_of_loan_paid_off, 0, 0, $month_id]);
                    if ($check) {
                        $check = $this->re_calculate_monthly_pending_loan($customer_id, $loan_no);
                        if(!$check){
                            return back()->with('error', 'Somethin Went Wrong.....');
                        }

                        foreach ($loan_data as $curr_loan_data) {
                            $loan_id = $curr_loan_data->id;
                            $new_pending = $curr_loan_data->pending_loan + $amount;
                        }
                        $check = DB::update('UPDATE `loan` SET `pending_loan`= ?,`interest`= ?, `is_interest_calculate`=? WHERE id = ?', [$new_pending, 0, 0, $loan_id]);
                        if ($check) {
                            $check = DB::delete('DELETE FROM `loan_statement` WHERE id = ?', [$id]);
                            if ($check) {
                                return back()->with('message', 'Transaction Cancel Sucessfully.....');
                            } else {
                                return back()->with('error', 'Something Went Wrong.....');
                            }
                        } else {
                            return back()->with('error', 'Something Went Wrong.....');
                        }
                    } else {
                        return back()->with('error', 'Something Went Wrong.....');
                    }
                }
            } else {
                return back()->with('error', 'Data Not Found One.....');
            }
        } else {
            return back()->with('error', 'Data Not Found Two.....');
        }
    }



    // Function For Loan Statement data
    public function get_Loan_statement($loan_no, $customer_id)
    {
        $loan_data = DB::select('SELECT * FROM loan WHERE `loan_no`=? AND `customer_id`=?', [$loan_no,$customer_id]);
        if (count($loan_data) != 0) {
            foreach ($loan_data as $loan_data) {
                $pending_loan = $loan_data->amount;
                $interest = $loan_data->interest;
                $status = $loan_data->status;
            }
            if ($status == 1) {
                $pending_loan = $pending_loan + $interest;
            }
            $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$customer_id]);
            $loan_statement_data = DB::select('SELECT * FROM `loan_statement` WHERE `loan_no`=? AND `customer_id`=?', [$loan_no, $customer_id]);

            if (count($customer_data) != 0) {
                return view('admin.pages.loan_statement', array('customer_data' => $customer_data, 'sr' => 0, 'loan_data' => $loan_data, 'loan_statement_data' => $loan_statement_data, 'pending' => $pending_loan));
            } else {
                return back()->with('error', 'Data Not Found.....');
            }
        } else {
            return back()->with('error', 'Data Not Found.....');
        }
    }

    // Function For Monthly Loan Statement data
    public function get_monthly_loan_statement($loan_no, $customer_id)
    {
        $loan_data = DB::select('SELECT * FROM loan WHERE `loan_no`=? AND `customer_id`=?', [$loan_no,$customer_id]);
        if (count($loan_data) != 0) {
            foreach ($loan_data as $loan_data) {
                $customer_id = $loan_data->customer_id;
            }
            $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$customer_id]);
            $monthly_loan_statement_data = DB::select('SELECT * FROM `loan_monthly_status` WHERE `loan_no`=? AND `customer_id`=?', [$loan_no, $customer_id]);
            if (count($customer_data) == 0) {
                return back()->with('error', 'Data Not Found.....');
            } else {
                return view('admin.pages.monthly_loan_statement', array('customer_data' => $customer_data, 'sr' => 0, 'monthly_loan_statement' => $monthly_loan_statement_data));
            }
        } else {
            return back()->with('error', 'Data Not Found.....');
        }
    }

    public function get_collection_form_details($loan_month_id)
    {
        $loan_data = DB::select('SELECT * FROM loan_monthly_status WHERE `id`=?', [$loan_month_id]);
        if (count($loan_data) != 0) {
            foreach ($loan_data as $loan_data) {
                $customer_id = $loan_data->customer_id;
            }
            $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$customer_id]);
            if (count($customer_data) == 0) {
                return back()->with('error', 'Data Not Found.....');
            } else {
                return view('admin.pages.collect_forgot_loan', array('customer_data' => $customer_data, 'month_id' => $loan_month_id));
            }
        } else {
            return back()->with('error', 'Data Not Found.....');
        }
    }

    public function check_pin($conf_pin){
        $admin_data = DB::select('SELECT * FROM `admin`');
            foreach ($admin_data as $a_data) {
                $pin = $a_data->pin;
            }
        if($conf_pin == $pin){
            return true;
        }else{
            return false;
        }
    }

    public function re_calculate_monthly_pending_loan($customer_id, $loan_no)
    {
        $loan_details = DB::select('SELECT * FROM loan WHERE `customer_id`=? AND `loan_no`=?', [$customer_id,$loan_no]);
        if (count($loan_details) != 0) {
            foreach ($loan_details as $loan_data) {
                $monthly_pending_loan = $loan_data-> amount;
            }
        } else {
            return false;
        }

        $all_months_loan_status = DB::select('SELECT * FROM loan_monthly_status WHERE `customer_id`=? AND `loan_no`=?', [$customer_id, $loan_no]);
        if (count($all_months_loan_status) != 0) {
            foreach ($all_months_loan_status as $loan_data) {
                $perticular_month_loan_id = $loan_data->id;
                $pending_loan = $monthly_pending_loan - ($loan_data->amount_of_loan_paid_off);
                DB::update(
                    'UPDATE `loan_monthly_status` SET `monthly_pending_loan`=?, `pending_loan`=?, `interest` =?, `interest_is_calculate` = ? WHERE id =?',
                    [$monthly_pending_loan, $pending_loan, 0, 0, $perticular_month_loan_id]
                );
                $monthly_pending_loan = $pending_loan;
            }
            return true;
        } else {
            return false;
        }
    }

    public function submit_forgot_loan_collection(Request $request, $loan_month_id)
    {
        $amount = $request->input('amount');
        $details = $request->input('details');
        $con_pin = $request->input('con_pin');

        $check = $this->check_pin($con_pin);
        if(!$check){
            return back()->with('error', 'Pin Not Match.....');
        }


        $perticular_month_loan_data = DB::select('SELECT * FROM loan_monthly_status WHERE `id`=?', [$loan_month_id]);
        if (count($perticular_month_loan_data) != 0) {
            foreach ($perticular_month_loan_data as $loan_data) {
                $perticular_month_loan_id = $loan_data->id;
                $customer_id = $loan_data->customer_id;
                $account_no = $loan_data->account_no;
                $loan_no = $loan_data->loan_no;
                $amount_of_loan_paid_off = $loan_data->amount_of_loan_paid_off;
            }

            $loan_details = DB::select('SELECT * FROM loan WHERE `customer_id`=? AND `loan_no`=?', [$customer_id,$loan_no]);
            if (count($loan_details) != 0) {
                foreach ($loan_details as $loan_data) {
                    $loan_id = $loan_data-> id;
                    $previous_amount = $loan_data->amount;
                    $pending_loan = $loan_data->pending_loan;
                }
                if($pending_loan-$amount < 0){
                    return back()->with('error', 'Amount should be less than pending amount.....');
                }
                $check = DB::update(
                        'UPDATE `loan_monthly_status` SET `amount_of_loan_paid_off` = ? WHERE id =?',
                        [$amount_of_loan_paid_off + $amount, $perticular_month_loan_id]
                    );
                if($check){

                    $check = $this->re_calculate_monthly_pending_loan($customer_id, $loan_no);
                    if(!$check){
                        return back()->with('error', 'Somethin Went Wrong.....');
                    }

                    $check = DB::update(
                        'UPDATE `loan` SET `pending_loan` = ?, `interest` =?, `is_interest_calculate`=? WHERE id =?',
                        [$pending_loan - $amount, 0, 0, $loan_id]
                    );
                    if($check){
                        date_default_timezone_set('Asia/Kolkata');
                        $date = date("d-m-Y");
                        $time = Date("h:i:s");
                        $loan_statement = array(
                            'loan_no' => $loan_no, 'customer_id' => $customer_id, 'month_id' => $perticular_month_loan_id, 'account_no' => $account_no, 'amount' => $amount, 'details'=> $details, 'date' => $date, 'time' => $time
                        );
                        $check = DB::table('loan_statement')->insert($loan_statement);
                        if ($check) {
                            return back()->with('message', 'Loan Collection Added Successfully.....');
                        } else {
                            return back()->with('error', 'Somethin Went Wrong.....');
                        }
                    }else {
                        return back()->with('error', 'Something Went Wrong.....');
                    }
                }else {
                    return back()->with('error', 'Something Went Wrong.....');
                }
            } else {
                return back()->with('error', 'Loan Data Not Found.....');
            }
        } else {
            return back()->with('error', 'Data Not Found.....');
        }
    }
}
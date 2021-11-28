<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use SebastianBergmann\Environment\Console;

class statement_controller extends Controller
{
    // Function For customer Data
    function get_customer_info($id)
    {
        $customer_data = DB::select('SELECT * FROM customers where id=?', [$id]);
        $cuss_data['cuss_data'] = $customer_data;
        echo json_encode($cuss_data);
        exit;
    }

    function get_pending_loan($id)
    {
        $pending_loan_amount = DB::select('SELECT `id`,`pending` FROM `bachat_monthly` WHERE `customer_id`=? AND `is_received`=? AND `is_expire`=? ORDER BY id ASC LIMIT ?', [$id, 0, 1, 1]);

        $loan_data['loan_data'] = $pending_loan_amount;
        echo json_encode($loan_data);
        exit;
    }

    // Function For Submit Collection
    public function submit_cutomers_collection(Request $request)
    {
        try {
            $cuss_id = $request->input('customer_id');
            $collection_of = $request->input('collection');
            $amount = $request->input('amount');
            $con_pin = $request->input('con_pin');
            $status = "cr";
            $admin_data = DB::select('SELECT * FROM `admin`');
            foreach ($admin_data as $a_data) {
                $pin = $a_data->pin;
            }

            $cuss_data = DB::select('SELECT * FROM `customers` WHERE id=?', [$cuss_id]);
            foreach ($cuss_data as $a_data) {
                $acc_no = $a_data->acc_no;
                $is_active = $a_data->is_active;
                $email = $a_data->email;
            }
            if (count($cuss_data) != 0) {
                if ($con_pin == $pin) {
                    if ($collection_of == 1) {
                        if ($is_active != 0) {
                            $expire_month_check = DB::select('SELECT * FROM `bachat_monthly` WHERE customer_id = ? AND is_received = ? AND is_expire = ?', [$cuss_id, 0, 1]);
                            if (count($expire_month_check) == 0) {
                                $curr_month_check = DB::select('SELECT * FROM `bachat_monthly` WHERE customer_id = ? AND is_expire = ?', [$cuss_id, 0]);
                                if (count($curr_month_check) != 0) {
                                    foreach ($curr_month_check as $curr_month_bachat) {
                                        $curr_month_id = $curr_month_bachat->id;
                                        $credited_amount = $curr_month_bachat->credited;
                                        $pending_amount = $curr_month_bachat->pending;
                                    }
                                    // if ($amount > $pending_amount) {
                                    //         return back()->with('error', 'Please Check Pending Amount.....');
                                    // }
                                    $new_credited_amount = $credited_amount + $amount;
                                    $new_pending_amount = $pending_amount - $amount;

                                    if ($new_pending_amount <= 0) {
                                        $check = DB::update('UPDATE `bachat_monthly` SET `credited`= ?,`pending`=?,	`is_received`=? WHERE id = ?', [$new_credited_amount, 0, 1, $curr_month_id]);
                                        if (!$check) {
                                            return back()->with('error', 'Something Went Wrong.....');
                                        }
                                    } else {
                                        $check = DB::update('UPDATE `bachat_monthly` SET `credited`= ?,`pending`=? WHERE id = ?', [$new_credited_amount, $new_pending_amount, $curr_month_id]);
                                        if (!$check) {
                                            return back()->with('error', 'Something Went Wrong.....');
                                        }
                                    }
                                    foreach ($cuss_data as $bachat_data) {
                                        $old_bachat_balance = $bachat_data->balance;
                                    }
                                    date_default_timezone_set('Asia/Kolkata');
                                    $date = date("d-m-Y");
                                    $time = Date("h:i:s");
                                    $new_bachat_balance = $old_bachat_balance + $amount;
                                    $statement = array(
                                        'month_id' => $curr_month_id, 'customer_id' => $cuss_id, 'account_no' => $acc_no, 'amount' => $amount, 'date' => $date, 'time' => $time, 'status' => $status,
                                    );
                                    $check = DB::table('statement')->insert($statement);
                                    if ($check) {
                                        $check = DB::update('UPDATE `customers` SET `balance`= ? WHERE id = ?', [$new_bachat_balance, $cuss_id]);
                                        if ($check) {
                                            $request->session()->put('email', $email);
                                            $request->session()->put('acc_no', $acc_no);
                                            $request->session()->put('conn_amount', $amount);
                                            $request->session()->put('conn_collection_of', $collection_of);
                                            $request->session()->put('collection_for', 'Daily Collection');
                                            $request->session()->put('date', $date);
                                            $request->session()->put('time', $time);
                                            return redirect('send_email');
                                        } else {
                                            return back()->with('error', 'Something Went Wrong.....');
                                        }
                                    } else {
                                        return back()->with('error', 'Something Went Wrong.....');
                                    }
                                } else {
                                    return back()->with('error', 'Collect Collection After 2 Hours.....');
                                }
                            } else {
                                return back()->with('error', 'Collect Penalty And Pending Amount First.....');
                            }
                        } else {
                            return back()->with('error', 'Account Is Expire.....');
                        }
                    } elseif ($collection_of == 2) {
                        $loan_data = DB::select('SELECT * FROM `loan` WHERE customer_id = ? AND `status`=?', [$cuss_id, 0]);
                        $montyly_data = DB::select('SELECT * FROM `loan_monthly_status` WHERE customer_id = ? AND `is_expire`=?', [$cuss_id, 0]);
                        if ((count($loan_data) != 0) && (count($montyly_data) != 0)) {
                            foreach ($loan_data as $loan_data) {
                                $loan_id = $loan_data->id;
                                $loan_no = $loan_data->loan_no;
                                $old_pending_loan = $loan_data->pending_loan;
                                $old_loan_interest = $loan_data->interest;
                            }
                            foreach ($montyly_data as $loan_monthly_data) {
                                $monthly_lone_id = $loan_monthly_data->id;
                                $prev_amount_of_loan_paid_off = $loan_monthly_data->amount_of_loan_paid_off;
                                $curr_month_interest = $loan_monthly_data->interest;
                            }

                            $new_amount_of_loan_paid_off = $prev_amount_of_loan_paid_off + $amount;

                            if (($old_pending_loan >= $amount) && ($old_pending_loan != 0)) {
                                $new_pending_loan = $old_pending_loan - $amount;

                                if ($new_pending_loan <= 0) {
                                    return back()->with('error', 'Nill The Loan From Profile.....');
                                }

                                if ($curr_month_interest > 0) {
                                    $curr_month_new_interest = 0;
                                    $check = DB::update('UPDATE `loan_monthly_status` SET `amount_of_loan_paid_off`= ?,`pending_loan`= ?,`interest`= ?,`interest_is_calculate`= ? WHERE id = ?', [$new_amount_of_loan_paid_off, $new_pending_loan, $curr_month_new_interest, 0, $monthly_lone_id]);
                                    if ($check) {
                                        $new_loan_interest = $old_loan_interest - $curr_month_interest;

                                        $check = DB::update('UPDATE `loan` SET `pending_loan`= ?, `interest`= ? WHERE id = ?', [$new_pending_loan, $new_loan_interest, $loan_id]);
                                        if (!$check) {
                                            return back()->with('error', 'Something Went Wrong1.....');
                                        }
                                    } else {
                                        return back()->with('error', 'Something Went Wrong3.....');
                                    }
                                } else {
                                    $check = DB::update('UPDATE `loan_monthly_status` SET `amount_of_loan_paid_off`= ?, `pending_loan`=? WHERE id = ?', [$new_amount_of_loan_paid_off, $new_pending_loan, $monthly_lone_id]);
                                    if ($check) {
                                        $check = DB::update('UPDATE `loan` SET `pending_loan`= ? WHERE id = ?', [$new_pending_loan, $loan_id]);
                                        if (!$check) {
                                            return back()->with('error', 'Something Went Wrong4.....');
                                        }
                                    } else {
                                        return back()->with('error', 'Something Went Wrong6.....');
                                    }
                                }
                                date_default_timezone_set('Asia/Kolkata');
                                $date = date("d-m-Y");
                                $time = Date("h:i:s");
                                $loan_statement = array(
                                    'loan_no' => $loan_no, 'customer_id' => $cuss_id, 'month_id' => $monthly_lone_id, 'account_no' => $acc_no, 'amount' => $amount, 'date' => $date,
                                    'time' => $time
                                );
                                $check = DB::table('loan_statement')->insert($loan_statement);
                                if ($check) {
                                    $request->session()->put('email', $email);
                                    $request->session()->put('acc_no', $acc_no);
                                    $request->session()->put('conn_amount', $amount);
                                    $request->session()->put('conn_collection_of', $collection_of);
                                    $request->session()->put('collection_for', 'Loan Collection');
                                    $request->session()->put('date', $date);
                                    $request->session()->put('time', $time);

                                    return redirect('send_email');
                                } else {
                                    return back()->with('error', 'Somethin Went Wrong.....');
                                }
                            } else {
                                return back()->with('error', 'Check The Pending Loan.....');
                            }
                        } else {
                            return back()->with('error', 'Loan is Not Pending.....');
                        }
                    } elseif ($collection_of == 3) {
                        $expire_month_check = DB::select('SELECT * FROM `bachat_monthly` WHERE customer_id = ? AND is_received = ? AND is_expire = ? ORDER BY id LIMIT ?', [$cuss_id, 0, 1, 1]);
                        if (count($expire_month_check) != 0) {
                            foreach ($expire_month_check as $curr_month_bachat) {
                                $expire_month_id = $curr_month_bachat->id;
                                $credited_amount = $curr_month_bachat->credited;
                                $pending_amount = $curr_month_bachat->pending;
                                $penalty = $curr_month_bachat->penalty;
                            }

                            $new_credited_amount = $credited_amount + $amount;
                            $new_pending_amount = $pending_amount - $amount;

                            if ($new_pending_amount < 0) {
                                return back()->with('error', 'Please Check Pending Amount');
                            }
                            if ($new_pending_amount <= 0) {
                                $check = DB::update('UPDATE `bachat_monthly` SET `credited`= ?,`pending`=?,	`is_received`=? WHERE id = ?', [$new_credited_amount, 0, 1, $expire_month_id]);
                                if (!$check) {
                                    return back()->with('error', 'Something Went Wrong.....');
                                }
                            } else {
                                $check = DB::update('UPDATE `bachat_monthly` SET `credited`= ?,`pending`=? WHERE id = ?', [$new_credited_amount, $new_pending_amount, $expire_month_id]);
                                if (!$check) {
                                    return back()->with('error', 'Something Went Wrong.....');
                                }
                            }
                            if ($penalty > 0) {
                                $check = DB::update('UPDATE `bachat_monthly` SET `penalty`= ?,`is_calculate`= ? WHERE id = ?', [0, 0, $expire_month_id]);
                            }
                            foreach ($cuss_data as $bachat_data) {
                                $old_bachat_balance = $bachat_data->balance;
                            }
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date("d-m-Y");
                            $time = Date("h:i:s");
                            $new_bachat_balance = $old_bachat_balance + $amount;
                            $statement = array(
                                'month_id' => $expire_month_id, 'customer_id' => $cuss_id, 'account_no' => $acc_no, 'amount' => $amount, 'date' => $date,
                                'time' => $time, 'status' => $status,
                            );
                            $check = DB::table('statement')->insert($statement);
                            if ($check) {
                                $check = DB::update('UPDATE `customers` SET `balance`= ? WHERE id = ?', [$new_bachat_balance, $cuss_id]);
                                if ($check) {
                                    $request->session()->put('email', $email);
                                    $request->session()->put('acc_no', $acc_no);
                                    $request->session()->put('conn_amount', $amount);
                                    $request->session()->put('conn_collection_of', $collection_of);
                                    $request->session()->put('collection_for', 'Pending Collection');
                                    $request->session()->put('date', $date);
                                    $request->session()->put('time', $time);

                                    return redirect('send_email');
                                } else {
                                    return back()->with('error', 'Something Went Wrong.....');
                                }
                            } else {
                                return back()->with('error', 'Something Went Wrong.....');
                            }
                        } else {
                            return back()->with('error', 'There Is Not Pending Amount.....');
                        }
                    }
                } else {
                    return back()->with('error', 'Pin Not Match.....');
                }
            } else {
                return back()->with('error', 'Customer Not Found.....');
            }
        } catch (Exception $th) {
            dd($th->getMessage());
        }
    }

    // Function For show todays collection
    public function show_todays_collection(Request $request)
    {
        $today = Date('d-m-Y');
        $total_collection = DB::select("SELECT SUM(amount) as colle FROM `statement` WHERE `date` = ?",[$today]);

        foreach($total_collection as $data){
            if($data->colle == null){
                $total_colle = 0;
            }else{
                $total_colle = $data->colle;
            }
        }

        $total_loan_collection = DB::select("SELECT SUM(amount) as loan FROM `loan_statement` WHERE `date` = ?",[$today]);
        foreach($total_loan_collection as $data){
            if($data->loan == null){
                $total_loan_colle = 0;
            }else{
                $total_loan_colle = $data->loan;
            }
        }
        $todays_collection = DB::select(
            "SELECT * FROM customers cust
            LEFT JOIN
            (SELECT SUM(amount) as coll, customer_id FROM statement sub_stat WHERE sub_stat.date = ? GROUP BY sub_stat.customer_id) as stat
            ON cust.id = stat.customer_id
            LEFT JOIN
            (SELECT SUM(amount) as loan, customer_id FROM loan_statement loan_stat WHERE loan_stat.date = ? GROUP BY loan_stat.customer_id) as loan
            ON cust.id = loan.customer_id",[$today, $today]);

            return view('admin.pages.todays_collection', array('todays_collection' => $todays_collection, 'total_collection' => $total_colle, 'total_loan_collection' => $total_loan_colle));
    }

    // Function For Statement data
    public function get_statement($id)
    {
        $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$id]);
        $statement_data = DB::select('SELECT * FROM `statement` WHERE `customer_id`=?', [$id]);
        if ((count($customer_data) == 0 || ($statement_data) == 0)) {
            return back()->with('error', 'Data Not Found.....');
        } else {
            return view('admin.pages.statement', array('customer_data' => $customer_data, 'sr' => 0, 'statement_data' => $statement_data, 'balance' => 0));
        }
    }
    // Function For cancel transaction
    public function cancel_transaction($id)
    {
        $statement_data = DB::select('SELECT * FROM `statement` WHERE `id`=?', [$id]);
        if (count($statement_data) != 0) {
            foreach ($statement_data as $stat_data) {
                $customer_id = $stat_data->customer_id;
                $month_id = $stat_data->month_id;
                $amount = $stat_data->amount;
                $date = $stat_data->date;
            }
            $monthly_data = DB::select('SELECT * FROM `bachat_monthly` WHERE `id`=? AND `customer_id`=?', [$month_id, $customer_id]);
            $customer_data = DB::select('SELECT * FROM `customers` WHERE `id`=?', [$customer_id]);
            if ((count($monthly_data) != 0) && (count($customer_data) != 0)) {
                foreach ($monthly_data as $month_data) {
                    $month_id = $month_data->id;
                    $monthly_bachat_amount = $month_data->monthly_bachat_amount;
                    $prev_credited = $month_data->credited;
                    $prev_pending = $month_data->pending;
                    $penalty = $month_data->penalty;

                    $new_credited = $prev_credited - $amount;
                    $new_pending = $monthly_bachat_amount - $new_credited;

                    if ($new_pending < 0) {
                        $new_pending = 0;
                    }
                    if ($monthly_bachat_amount > $new_credited) {
                        $check = DB::update('UPDATE `bachat_monthly` SET `credited`= ?,`pending`= ?,`is_received`= ? WHERE id = ?', [$new_credited, $new_pending, 0, $month_id]);
                    } else {
                        $check = DB::update('UPDATE `bachat_monthly` SET `credited`= ? WHERE id = ?', [$new_credited, $month_id]);
                    }
                    if ($penalty > 0) {
                        $check = DB::update('UPDATE `bachat_monthly` SET `penalty`= ?,`is_calculate`= ? WHERE id = ?', [0, 0, $month_id]);
                    }

                    if ($check) {
                        foreach ($customer_data as $cust_data) {
                            $prev_balance = $cust_data->balance;
                        }
                        $new_balance = $prev_balance - $amount;
                        $check = DB::update('UPDATE `customers` SET `balance`= ? WHERE id = ?', [$new_balance, $customer_id]);
                        if ($check) {
                            $check = DB::delete('DELETE FROM `statement` WHERE id = ?', [$id]);
                            return back()->with('message', 'Transaction Cancel Sucessfully.....');
                        } else {
                            return back()->with('error', 'Something Went Wrong.....');
                        }
                    } else {
                        return back()->with('error', 'Month Is Expire.....');
                    }
                }
            } else {
                return back()->with('error', 'Data Not Found.....');
            }
        } else {
            return back()->with('error', 'Data Not Found.....');
        }
    }

     // Function For Calculate Penelty
    public function calculate_penalty($id)
    {
        $penalty_month = DB::select('SELECT * FROM bachat_monthly WHERE `id`=?', [$id]);
        if (count($penalty_month) != 0) {
            foreach ($penalty_month as $prev_month_bachat) {
                $pending = $prev_month_bachat->pending;
                $end_date = $prev_month_bachat->end_date;
            }
            $compaire_date = $end_date;
            $today = Date('d-m-Y');
            $dateTimestamp1 = strtotime($compaire_date);
            $dateTimestamp2 = strtotime($today);
            $pending_months = 0;
            while ($dateTimestamp1 < $dateTimestamp2) {
                $pending_months++;
                $date = new DateTime($compaire_date);
                $date->modify('+1 month');
                $compaire_date = $date->format('d-m-Y');
                $dateTimestamp1 = strtotime($compaire_date);
            }

            $penalty = (($pending / 100) * $pending_months);
            $check = DB::update('UPDATE `bachat_monthly` SET `penalty`=?,`is_calculate`=? WHERE id = ?', [$penalty, 1, $id]);
            if ($check) {
                return back()->with('message', 'Penalty Calculated Successfully.....');
            } else {
                return back()->with('error', 'Problem In Penalty Calulation.....');
            }
        } else {
            return back()->with('error', 'Data Not Found.....');
        }
    }

    // Function For Statement data
    public function collect_penalty(request $request, $id)
    {

        $penalty_month = DB::select('SELECT * FROM bachat_monthly WHERE `id`=? ', [$id]);
        if (count($penalty_month) != 0) {
            foreach ($penalty_month as $prev_month_bachat) {
                $cuss_id = $prev_month_bachat->customer_id;
                $pending = $prev_month_bachat->pending;
                $penalty = $prev_month_bachat->penalty;
                $penalty_credited_date = $prev_month_bachat->penalty_credited_date;
            }
            $customer_data = DB::select('SELECT * FROM `customers` WHERE id = ?', [$cuss_id]);
            if (count($customer_data) != 0) {
                if ($penalty_credited_date == 0) {
                    $total = $pending + $penalty;
                    if (count($penalty_month) != 0) {
                        return view('admin.pages.collect_penalty', array('data' => 1, 'pending_month_id' => $id, 'customer_data' => $customer_data, 'penalty' => $penalty,  'pending' => $pending, 'total' => $total));
                    } else {
                        return back()->with('error', 'Data Not Found.....');
                    }
                } else {
                    return view('admin.pages.collect_penalty', array('data' => 1, 'pending_month_id' => $id, 'customer_data' => $customer_data, 'penalty' => 0,  'pending' => 0, 'total' => 0));
                }
            } else {
                return back()->with('error', 'Data Not Found.....');
            }
        } else {
            return back()->with('error', 'Data Not Found.....');
        }
    }

    // Function For Statement data
    public function submit_penalty(request $request, $penalty_month_id)
    {

        $credited_amount = $request->input('pending_amount');
        if ($credited_amount != 0) {
            $conf_pin = $request->input('con_pin');

            $pin_data = DB::select('SELECT * FROM `admin`');
            if (count($pin_data) != 0) {
                foreach ($pin_data as $admin_pin_data) {
                    $current_pin = $admin_pin_data->pin;
                }
                if ($current_pin == $conf_pin) {
                    $penalty_month_cerdit_amount = DB::select('SELECT * FROM `bachat_monthly` WHERE id = ?', [$penalty_month_id]);
                    if (count($penalty_month_cerdit_amount) != 0) {
                        foreach ($penalty_month_cerdit_amount as $credited_data) {
                            $prev_creadited_amount = $credited_data->credited;
                            $cuss_id = $credited_data->customer_id;
                        }
                        $curr_creadited_amount = $prev_creadited_amount + $credited_amount;

                        $cuss_data = DB::select('SELECT * FROM `customers` WHERE id=?', [$cuss_id]);

                        if (count($cuss_data) != 0) {
                            foreach ($cuss_data as $a_data) {
                                $prev_balance = $a_data->balance;
                                $acc_no = $a_data->acc_no;
                            }

                            $new_balance = $prev_balance + $credited_amount;
                            $check = DB::update('UPDATE `customers` SET `balance`= ? WHERE id =?', [$new_balance, $cuss_id]);
                            if ($check) {
                                date_default_timezone_set('Asia/Kolkata');
                                $date = date("d-m-Y");
                                $time = Date("h:i:s");
                                $statement = array(
                                    'month_id' => $penalty_month_id, 'customer_id' => $cuss_id, 'account_no' => $acc_no, 'amount' => $credited_amount, 'date' => $date,
                                    'time' => $time, 'status' => 'cr',
                                );
                                $check = DB::table('statement')->insert($statement);
                                if ($check) {
                                    $today = Date('d-m-Y');
                                    $check = DB::update('UPDATE `bachat_monthly` SET `credited`= ?,`is_received`=?, `penalty_credited_date`=? WHERE id =?', [$curr_creadited_amount, 1, $today, $penalty_month_id]);
                                    if ($check) {
                                        return back()->with('message', 'Penalty And Pending Amount Collected Successfully.....');
                                    } else {
                                        return back()->with('error', 'Something Went Wrong one.....');
                                    }
                                } else {
                                    return back()->with('error', 'Something Went Wrong two.....');
                                }
                            } else {
                                return back()->with('error', 'Something Went Wrong three.....');
                            }
                        } else {
                            return back()->with('error', 'Data Not Found.....');
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
        } else {
            return back()->with('error', 'Allready Amount Is Collected.....');
        }
    }

    // Function For Monthly Statement data
    public function get_monthly_statement_data($id)
    {
        $customer_data = DB::select('SELECT * FROM customers WHERE `id`=?', [$id]);
        $monthly_statement_data = DB::select('SELECT * FROM `bachat_monthly` WHERE `customer_id`=? AND `is_received`=?', [$id, 1]);
        if ((count($customer_data) == 0 || ($monthly_statement_data) == 0)) {
            return back()->with('error', 'Data Not Found.....');
        } else {
            return view('admin.pages.monthly_bachat_statement', array('customer_data' => $customer_data, 'sr' => 0, 'monthly_statement_data' => $monthly_statement_data));
        }
    }
}

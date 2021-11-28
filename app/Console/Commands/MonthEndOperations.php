<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MonthEndOperations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:month_end_operations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'month end operations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customer_data = DB::select('SELECT * FROM customers');
        if (count($customer_data) != 0) {
            foreach ($customer_data as $cuss_data) {
                $cuss_id = $cuss_data->id;
                $is_active = $cuss_data->is_active;
                $acc_expire_date = $cuss_data->acc_expire_date;
            }
            if ($is_active == 1) {
                $today = Date('d-m-Y');
                $dateTimestamp1 = strtotime($acc_expire_date);
                $dateTimestamp2 = strtotime($today);
                if ($dateTimestamp1 < $dateTimestamp2) {
                    DB::update('UPDATE `customers` SET `is_active`=? WHERE id = ?', [0, $cuss_id]);
                }
            }
        }
        $customer_data = DB::select('SELECT * FROM customers');

        if (count($customer_data) != 0) {
            foreach ($customer_data as $cuss_data) {
                $cuss_id = $cuss_data->id;
                $acc_no = $cuss_data->acc_no;
                $is_active = $cuss_data->is_active;
                $mon_bachat_amount = $cuss_data->per_month_bachat;

                if ($is_active == 1) {
                    $monthly_bachat = DB::select('SELECT * FROM bachat_monthly WHERE `customer_id`=?', [$cuss_id]);
                    if (count($monthly_bachat) == 0) {
                        $start_date = $cuss_data->acc_open_date;

                        $date = new DateTime($start_date);
                        $date->modify('+1 month');
                        $next_start_date = $date->format('d-m-Y');
                        $date->modify('-1 day');
                        $end_date = $date->format('d-m-Y');
                        $save_cuur_month = array(
                            'customer_id' => $cuss_id, 'account_no' => $acc_no, 'monthly_bachat_amount' => $mon_bachat_amount, 'pending' => $mon_bachat_amount,
                            'start_date' => $start_date, 'end_date' => $end_date, 'new_month_start_date' => $next_start_date
                        );
                        DB::table('bachat_monthly')->insertGetId($save_cuur_month);
                    }
                }
                $i = 1;
                while ($i != 0) {
                    $monthly_bachat = DB::select('SELECT * FROM bachat_monthly WHERE `customer_id`=? AND `is_expire`=? ', [$cuss_id, 0]);
                    if (count($monthly_bachat) != 0) {
                        foreach ($monthly_bachat as $last_month_data) {
                            $month_id = $last_month_data->id;
                            $prev_end_date = $last_month_data->end_date;

                            $today = Date('d-m-Y');
                            $dateTimestamp1 = strtotime($prev_end_date);
                            $dateTimestamp2 = strtotime($today);

                            if ($dateTimestamp1 <= $dateTimestamp2) {

                                DB::update('UPDATE `bachat_monthly` SET `is_expire`=? WHERE id = ?', [1, $month_id]);

                                $curr_month_start_date = $last_month_data->new_month_start_date;

                                $date = new DateTime($curr_month_start_date);
                                $date->modify('+1 month');
                                $new_month_start_date = $date->format('d-m-Y');

                                $date->modify('-1 day');
                                $end_date = $date->format('d-m-Y');

                                $save_cuur_month = array(
                                    'customer_id' => $cuss_id, 'account_no' => $acc_no, 'monthly_bachat_amount' => $mon_bachat_amount, 'pending' => $mon_bachat_amount,
                                    'start_date' => $curr_month_start_date, 'end_date' => $end_date, 'new_month_start_date' => $new_month_start_date
                                );
                                DB::table('bachat_monthly')->insertGetId($save_cuur_month);
                            } else {
                                $i = 0;
                            }
                        }
                    } else {
                        $i = 0;
                    }
                }
            }

            $curr_loan_data = DB::select('SELECT * FROM loan WHERE `status`=?', [0]);

            if (count($curr_loan_data) != 0) {
                foreach ($curr_loan_data as $curr_loan_info) {
                    $loan_id = $curr_loan_info->id;
                    $loan_no = $curr_loan_info->loan_no;
                    $customer_id = $curr_loan_info->customer_id;
                    $prev_interest = $curr_loan_info->interest;

                    $monthly_loan_data = DB::select('SELECT * FROM loan_monthly_status WHERE `loan_no`=? AND `customer_id`=? AND `is_expire`=?', [$loan_no, $customer_id, 0]);

                    if (count($monthly_loan_data) != 0) {
                        foreach ($monthly_loan_data as $monthly_loan_info) {
                            $month_end_date = $monthly_loan_info->end_date;
                            $interest_is_calculate = $monthly_loan_info->interest_is_calculate;

                            $today = Date('d-m-Y');
                            $dateTimestamp1 = strtotime($month_end_date);
                            $dateTimestamp2 = strtotime($today);

                            if ($dateTimestamp1 < $dateTimestamp2) {
                                $curr_month_id = $monthly_loan_info->id;
                                $customer_id = $monthly_loan_info->customer_id;
                                $account_no = $monthly_loan_info->account_no;
                                $loan_amount = $monthly_loan_info->loan_amount;
                                $loan_no = $monthly_loan_info->loan_no;
                                $prev_month_pending_loan = $monthly_loan_info->monthly_pending_loan;
                                $prev_pending_loan = $monthly_loan_info->pending_loan;
                                $curr_month_start_date = $monthly_loan_info->new_month_start_date;

                                if ($interest_is_calculate == 0) {

                                    $current_interest = (($prev_month_pending_loan / 100) * 2);

                                    $new_interest = $prev_interest + $current_interest;
                                    DB::update('UPDATE `loan_monthly_status` SET `interest`= ?,`interest_is_calculate`= ?, `is_expire`=? WHERE id = ?', [$current_interest, 1, 1, $curr_month_id]);

                                    echo $curr_month_start_date;
                                    $date = new DateTime($curr_month_start_date);
                                    $date->modify('+1 month');

                                    $new_month_start_date = $date->format('d-m-Y');
                                    echo $new_month_start_date;
                                    $date->modify('-1 day');
                                    $end_date = $date->format('d-m-Y');
                                    echo $end_date;
                                    $monthly_loan_data = array(
                                        'loan_no' => $loan_no, 'customer_id' => $customer_id, 'account_no' => $account_no, 'loan_amount' => $loan_amount,  'monthly_pending_loan' => $prev_pending_loan,
                                        'pending_loan' => $prev_pending_loan, 'start_date' => $curr_month_start_date, 'end_date' => $end_date, 'new_month_start_date' => $new_month_start_date
                                    );
                                    DB::table('loan_monthly_status')->insert($monthly_loan_data);
                                    DB::update('UPDATE `loan` SET `interest`=? WHERE id = ?', [$new_interest, $loan_id]);
                                } else {
                                    DB::update('UPDATE `loan_monthly_status` SET `is_expire`=? WHERE id = ?', [1, $curr_month_id]);
                                    if ($prev_pending_loan != 0) {

                                        $date = new DateTime($curr_month_start_date);
                                        $date->modify('+1 month');
                                        $new_month_start_date = $date->format('d-m-Y');

                                        // echo $new_month_start_date;
                                        $date->modify('-1 day');
                                        $end_date = $date->format('d-m-Y');
                                        $monthly_loan_data = array(
                                            'loan_no' => $loan_no, 'customer_id' => $customer_id, 'account_no' => $account_no, 'loan_amount' => $loan_amount,  'monthly_pending_loan' => $prev_pending_loan,
                                            'pending_loan' => $prev_pending_loan, 'start_date' => $curr_month_start_date, 'end_date' => $end_date, 'new_month_start_date' => $new_month_start_date
                                        );
                                        DB::table('loan_monthly_status')->insert($monthly_loan_data);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        // $round_data = array('round_count' => 1, 'win_no' => 1, 'win_x' => 1);

        // DB::table('demo')->insert($round_data);
        // /usr/local/bin/php /home/zb81qp0pk3rn/admin/artisan schedule:run
    }
}
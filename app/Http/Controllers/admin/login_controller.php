<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class login_controller extends Controller
{
    // Function For Login
    public function admin_login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        try {
            $logdata = DB::select("SELECT * FROM `admin` WHERE `username`= ?", [$username]);
            if (count($logdata) == 0) {
                return back()->with('error', 'Admin Data Not Found..');
            } else {
                foreach ($logdata as $data) {
                    if ($username == $data->username) {
                        if ($password == $data->password) {
                            $request->session()->put('user', $data->username);
                            return redirect('dashboard')->with($request->session()->get('user'));
                        } else {
                            return back()->with('error', 'Password Is Wrong.....');
                        }
                    } else {
                        return back()->with('error', 'Username Name Is Wrong.....');
                    }
                }
            }
        } catch (Exception $e) {
            dd($e->getMessage());
            dd('Something Went Wrong');
        }
    }

    // Function For Show Dashboard
    public function dashboard(Request $request)
    {
        try {
            $users = DB::select("SELECT count(id) as id FROM `customers`");
            foreach ($users as $data) {
                $totalUsers = $data->id;
            }

            $activeAccounts = DB::select("SELECT count(id) as id FROM `customers` WHERE `is_Active`=?",[1]);
            foreach ($activeAccounts as $data) {
                $totalActiveAccounts = $data->id;
            }

            $collection = DB::select("SELECT SUM(balance) as balance FROM `customers`");
            foreach ($collection as $data) {
                $totalCollection = $data->balance;
            }
            $disbursedLoan = DB::select("SELECT SUM(amount) as amount FROM `loan`");
            foreach ($disbursedLoan as $data) {
                $totalDisbursedLoan = $data->amount;
            }
            $pendingLoan = DB::select("SELECT SUM(pending_loan) as pending_loan FROM `loan`");
            foreach ($pendingLoan as $data) {
                $totalPendingLoan = $data->pending_loan;
            }

            $totalRecoveredLoan = ($totalDisbursedLoan - $totalPendingLoan);

            $collectedInterest = DB::select("SELECT SUM(interest) as interest FROM `loan` WHERE `status`=?",[1]);
            foreach ($collectedInterest as $data) {
                $totalCollectedInterest = $data->interest;
            }

            return view('admin.dashboard',
                    array('player_count' => 0,
                        'no_data' => 0,
                        'totalUsers' => $totalUsers,
                        'totalActiveAccounts' => $totalActiveAccounts,
                        'totalCollection' => $totalCollection,
                        'totalDisbursedLoan' => $totalDisbursedLoan,
                        'totalPendingLoan' => $totalPendingLoan,
                        'totalRecoveredLoan' => $totalRecoveredLoan,
                        'totalCollectedInterest' => $totalCollectedInterest));
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    // fUNCTION fOR Logout Admin
    function logout_admin(Request $request)
    {
        try {
            $request->session()->forget('admin');

            $request->session()->flush();
            return redirect('/admin');
        } catch (Exception $e) {
            dd($e->getMessage());
            dd('Something Went Wrong');
        }
    }
}
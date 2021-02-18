<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class customer_login_controller extends Controller
{
    // Function For Login
    public function customer_login(Request $request)
    {
        $accont_no = $request->input('acc_no');
        $password = $request->input('password');

        try {
            $logdata = DB::select("SELECT * FROM `customers` WHERE `acc_no`= ?", [$accont_no]);
            if (count($logdata) == 0) {
                return back()->with('error', 'customers Data Not Found..');
            } else {
                foreach ($logdata as $data) {
                    if ($accont_no == $data->acc_no) {
                        if ($password == $data->pass) {
                            $request->session()->put('acc_no', $data->acc_no);
                            return redirect('customers_dashboard')->with($request->session()->get('acc_no'));
                        } else {
                            return back()->with('error', 'Password Is Wrong.....');
                        }
                    } else {
                        return back()->with('error', 'accont Number Is Wrong.....');
                    }
                }
            }
        } catch (Exception $e) {
            dd($e->getMessage());
            dd('Something Went Wrong');
        }
    }

    // fUNCTION fOR Logout Admin
    function customer_logout(Request $request)
    {
        try {
            $request->session()->forget('user');

            $request->session()->flush();
            return redirect('/customer');
        } catch (Exception $e) {
            dd($e->getMessage());
            dd('Something Went Wrong');
        }
    }
}
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
            return view('admin.dashboard', array('player_count' => 0, 'no_data' => 0));
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
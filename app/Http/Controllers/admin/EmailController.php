<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Mail\Gmail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        try {
            $send_to = $request->session()->get('email');
            $conn_collection_of = $request->session()->get('conn_collection_of');

            $to = $send_to;
            if ($conn_collection_of == 1) {
                $details = [
                    'email' => $send_to,
                    'subject' => 'Confirmation Of Daily Collection',
                ];
            } else if ($conn_collection_of == 2) {
                $details = [
                    'email' => $send_to,
                    'subject' => 'Confirmation Of Loan Collection',
                ];
            } else {
                $details = [
                    'email' => $send_to,
                    'subject' => 'Confirmation Of Pending Collection'
                ];
            }

            Mail::to($to)->send(new Gmail($details));
            // Mail::send('admin.email.jagtapbachatgat_mail', $details, function ($message) use ($details) {
            //     $message->to($details['email'])
            //         ->subject($details['subject']);
            // });

            $request->session()->forget('email');
            $request->session()->forget('conn_amount');
            $request->session()->forget('acc_no');
            $request->session()->forget('time');
            $request->session()->forget('date');
            $request->session()->forget('collection_for');

            if ($conn_collection_of == 1) {
                $request->session()->forget('conn_collection_of');
                $request->session()->put('message', 'Collection Added Successfully.....');
                return view('admin.pages.collection');
            } else if ($conn_collection_of == 2) {
                $request->session()->forget('conn_collection_of');
                $request->session()->put('message', 'Loan Collection Added Successfully.....');
                return view('admin.pages.collection');
            } else {
                $request->session()->forget('conn_collection_of');
                $request->session()->put('message', 'Pending Collection Added Successfully.....');
                return view('admin.pages.collection');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
            dd('Something Went Wrong');
        }
    }
}
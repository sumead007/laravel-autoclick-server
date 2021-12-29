<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LineNotifyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function sent_message(Request $request)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        date_default_timezone_set("Asia/Bangkok");

        $sToken = "QRQDdlTYb0X0qroL2LIfPCOUOFds3QQ7tthglXq2iqU";
        $sMessage = $request->message;


        $chOne = curl_init();
        curl_setopt($chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify");
        curl_setopt($chOne, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($chOne, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($chOne, CURLOPT_POST, 1);
        curl_setopt($chOne, CURLOPT_POSTFIELDS, "message=" . $sMessage);
        $headers = array('Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $sToken . '',);
        curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($chOne, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($chOne);
        $message = null;
        //Result error 
        if (curl_error($chOne)) {
            $status =  'error:' . curl_error($chOne);
        } else {
            $result_ = json_decode($result, true);
            $status = "status : " . $result_['status'];
            $message = "message : " . $result_['message'];
        }
        curl_close($chOne);
        return response()->json(["status" => $status, "message" => $message]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\LineLogin;
use Illuminate\Http\Request;

class LineLoginOTPController extends Controller
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

    public function update_status_otp(Request $request, $id)
    {
        $data = Config::first();
        if ($data->status == 1) return response()->json(['code' => '422', 'message' => 'กรุณาปิดการใช้งานก่อน'], 422);
        LineLogin::find($id)->update(["otp" => $request->otp]);
        return response()->json(200);
    }

    public function get_all_post()
    {
        $datas = LineLogin::where("otp", "0")->get();
        return response()->json($datas);
    }
}

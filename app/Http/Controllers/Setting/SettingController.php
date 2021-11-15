<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;

class SettingController extends Controller
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

    public function index()
    {
        $data = Config::first();
        return view('setting', compact('data'));
    }

    public function store(Request $request)
    {
        //เพิ่มข้อมูลใหม่
        $request->validate(
            [
                "user_login" => "required|max:40",
                "password" => "required|min:8|max:20|",
            ],
        );

        $user = Config::updateOrCreate(['id' => 1], [
            "user_login" => $request->user_login,
            "password" => $request->password,
        ]);

        return response()->json(['code' => '200', 'message' => 'บันทึกข้อมูลสำเร็จ', 'data' => $user], 200);
    }

    public function update_status(Request $request)
    {
        $status = $request->status == 1 ? 0 : 1;
        $user = Config::updateOrCreate(['id' => 1], [
            "status" => $status,
        ]);
        return response()->json(['code' => '200', 'message' => 'บันทึกข้อมูลสำเร็จ', 'data' => $status], 200);
    }
}

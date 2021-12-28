<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\Line;
use App\Models\LineLogin;
use Carbon\Carbon;
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
        $datas2 = LineLogin::orderByDesc('created_at')->paginate(10);
        return view('setting', compact('data', 'datas2'));
    }

    public function store(Request $request)
    {
        $data = Config::first();
        if ($data->status == 0) {
            $user = Config::updateOrCreate(['id' => 1], [
                "status" => 0
            ]);
        } else {
            $user = Config::updateOrCreate(['id' => 0], [
                "status" => 1
            ]);
        }
        return response()->json(['code' => '200', 'message' => 'บันทึกข้อมูลสำเร็จ', 'data' => $user], 200);
    }

    public function store_line_login(Request $request)
    {
        $data = Config::first();
        if ($data->status == 1) return response()->json(['code' => '422', 'message' => 'กรุณาปิดการใช้งานก่อน'], 422);
        
        #อัพเดท
        if ($request->post_id != "") {
            $admin = LineLogin::find($request->post_id);
            $request->validate(
                [
                    "user_login" => $request->user_login != $admin->user_login ? "required|max:40|unique:line_logins" : "required|max:40",
                    "password" => "required|min:3|max:20|",
                ],
                [
                    // "type.required" => "กรุณากรอกช่องนี้",
                    // "id.required" => "กรุณากรอกช่องนี้",
                    // "id.min" => "ต้องมีตัวอักษรระหว่าง 1 - 30 ตัวอักษร",
                    // "id.max" => "ต้องมีตัวอักษรระหว่าง 1 - 30 ตัวอักษร",
                    // "id.unique" => "มีผู้ใช้แล้ว",

                    // "phone.required" => "กรุณากรอกช่องนี้",
                    // "phone.numeric" => "กรุณากรอกช่องนี้เป็นตัวเลข",
                    // "phone.digits" => "กรุณากรอกช่องนี้ 10 หลัก",
                    // "phone.unique" => "มีผู้ใช้แล้ว",

                ]
            );
            $user = LineLogin::updateOrCreate(['id' => $request->post_id], [
                "user_login" => $request->user_login,
                "password" => $request->password,
            ]);
        } else {
            //เพิ่มข้อมูลใหม่
            $request->validate(
                [
                    "user_login" => "required|max:40|unique:line_logins",
                    "password" => "required|min:3|max:20|",
                ],
                // [
                //     "type.required" => "กรุณากรอกช่องนี้",
                //     "id.required" => "กรุณากรอกช่องนี้",
                //     "id.min" => "ต้องมีตัวอักษรระหว่าง 1 - 30 ตัวอักษร",
                //     "id.max" => "ต้องมีตัวอักษรระหว่าง 1 - 30 ตัวอักษร",
                //     "id.unique" => "มีผู้ใช้แล้ว",

                //     "phone.required" => "กรุณากรอกช่องนี้",
                //     "phone.numeric" => "กรุณากรอกช่องนี้เป็นตัวเลข",
                //     "phone.digits" => "กรุณากรอกช่องนี้ 10 หลัก",
                //     "phone.unique" => "มีผู้ใช้แล้ว",

                // ]
            );
            $user = LineLogin::updateOrCreate(['id' => $request->post_id], [
                "user_login" => $request->user_login,
                "password" => $request->password,
            ]);
        }
        $user->created_at_2 = Carbon::parse($user->created_at)->locale('th')->diffForHumans();
        return response()->json(['code' => '200', 'message' => 'บันทึกข้อมูลสำเร็จ', 'data' => $user], 200);
    }

    public function get_addline_login($id)
    {
        $data = LineLogin::find($id);
        return response()->json($data);
    }

    public function update_status(Request $request)
    {
        $status = $request->status == 1 ? 0 : 1;
        $user = Config::updateOrCreate(['id' => 1], [
            "status" => $status,
        ]);
        return response()->json(['code' => '200', 'message' => 'บันทึกข้อมูลสำเร็จ', 'data' => $status], 200);
    }

    public function delete_post($id)
    {
        $data = Config::first();
        if ($data->status == 1) return response()->json(['code' => '422', 'message' => 'กรุณาปิดการใช้งานก่อน'], 422);

        $data = LineLogin::find($id)->delete();
        return response()->json(['sucess' => "ลบข้อมูลเรียบร้อย", "code" => "200"]);
    }

    public function select_user_login(Request $request)
    {
        $data = Config::first();
        if ($data->status == 1) return response()->json(['code' => '422', 'message' => 'กรุณาปิดการใช้งานก่อน'], 422);
        // return dd($request->status);
        $request->validate(
            [
                "select" => "required",
            ]
        );
        for ($i = 0; $i < count($request->select); $i++) {
            LineLogin::find($request->select[$i])->update(["status" => $request->status]);
        }

        return response()->json(['code' => '200', 'message' => 'บันทึกข้อมูลสำเร็จ', 'data' => ["id" => $request->select, "status" => $request->status]], 200);
    }
}

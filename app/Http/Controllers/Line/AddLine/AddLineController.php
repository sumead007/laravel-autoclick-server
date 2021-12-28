<?php

namespace App\Http\Controllers\Line\AddLine;

use App\Http\Controllers\Controller;
use App\Models\Line;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LineImport;
use App\Models\Config;

class AddLineController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $datas = Line::orderByDesc('created_at')->paginate(100);

        return view('line.add_line.home', compact('datas'));
    }

    public function store(Request $request)
    {
        $data = Config::first();
        if ($data->status == 1) return response()->json(['code' => '422', 'message' => 'กรุณาปิดการใช้งานก่อน'], 422);
        
        if ($request->post_id != "") {
            $admin = Line::find($request->post_id);
            $request->validate(
                [
                    "type" => "required",
                    "id" => $request->id != $admin->user_id ? "required|min:1|max:30|unique:lines,user_id" : "",
                    "phone" => $request->user_tel != $admin->user_tel ? ["required|digits:10|unique:lines,user_tel"] : "",
                ],
                [
                    "type.required" => "กรุณากรอกช่องนี้",
                    "id.required" => "กรุณากรอกช่องนี้",
                    "id.min" => "ต้องมีตัวอักษรระหว่าง 1 - 30 ตัวอักษร",
                    "id.max" => "ต้องมีตัวอักษรระหว่าง 1 - 30 ตัวอักษร",
                    "id.unique" => "มีผู้ใช้แล้ว",

                    "phone.required" => "กรุณากรอกช่องนี้",
                    "phone.numeric" => "กรุณากรอกช่องนี้เป็นตัวเลข",
                    "phone.digits" => "กรุณากรอกช่องนี้ 10 หลัก",
                    "phone.unique" => "มีผู้ใช้แล้ว",

                ]
            );
            $user = Line::updateOrCreate(['id' => $request->post_id], [
                "type" => $request->type,
                "user_id" =>   $request->id,
                "user_tel" =>  $request->phone,
            ]);
        } else {
            //เพิ่มข้อมูลใหม่
            $request->validate(
                [
                    "type" => "required",
                    "id" =>  "required|min:1|max:30|unique:lines,user_id",
                    "phone" => $request->type == 0 ? "required|unique:lines,user_tel" : "required|digits:10|unique:lines,user_tel",
                    // "telephone" => "required|numeric|digits:10|unique:users|unique:admins",
                    // "credit" => "required|numeric",
                    // "share_percentage" => 'required|numeric|between:0,99.99',
                ],
                [
                    "type.required" => "กรุณากรอกช่องนี้",
                    "id.required" => "กรุณากรอกช่องนี้",
                    "id.min" => "ต้องมีตัวอักษรระหว่าง 1 - 30 ตัวอักษร",
                    "id.max" => "ต้องมีตัวอักษรระหว่าง 1 - 30 ตัวอักษร",
                    "id.unique" => "มีผู้ใช้แล้ว",

                    "phone.required" => "กรุณากรอกช่องนี้",
                    "phone.numeric" => "กรุณากรอกช่องนี้เป็นตัวเลข",
                    "phone.digits" => "กรุณากรอกช่องนี้ 10 หลัก",
                    "phone.unique" => "มีผู้ใช้แล้ว",

                ]
            );


            $user = Line::updateOrCreate(['id' => $request->post_id], [
                "type" => $request->type,
                "user_id" => $request->id,
                "user_tel" =>  $request->phone,
                "status" => 0,

            ]);
        }
        $user->created_at_2 = Carbon::parse($user->created_at)->locale('th')->diffForHumans();
        return response()->json(['code' => '200', 'message' => 'บันทึกข้อมูลสำเร็จ', 'data' => $user], 200);
    }

    public function get_addline($id)
    {
        $data = Line::find($id);
        return response()->json($data);
    }

    public function delete_post($id)
    {
        $data = Config::first();
        if ($data->status == 1) return response()->json(['code' => '422', 'message' => 'กรุณาปิดการใช้งานก่อน'], 422);
        
        $data = Line::find($id)->delete();
        return response()->json(['sucess' => "ลบข้อมูลเรียบร้อย", "code" => "200"]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function fileImport(Request $request)
    {
        $data = Config::first();
        if ($data->status == 1) return response()->json(['code' => '422', 'message' => 'กรุณาปิดการใช้งานก่อน'], 422);
        
        $request->validate(
            [
                "fileid" => "required|mimes:csv,xlsx",
            ],
            [
                "fileid.required" => "กรุณาอัพโหลดไฟล์",
                "fileid.mimes" => "กรุณาอัพโหลดไฟล์ที่มีนามสกุล csv หรือ xlsx",
            ]
        );
        Excel::import(new LineImport(), $request->file('fileid')->store('temp'));
        return response()->json(['sucess' => "ลบข้อมูลเรียบร้อย"], 200);
    }
}

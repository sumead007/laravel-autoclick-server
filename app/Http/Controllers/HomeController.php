<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
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
        $datas = Message::orderBy('order', 'asc')->get();

        return view('home', compact('datas'));
    }

    public function save_change(Request $request)
    {
        for ($i = 0; $i < count($request->id); $i++) {
            Message::find($request->id[$i])->update(["order" => $i]);
        }
        return response()->json(["message" => "บันทึกข้อมูลสำเร็จ"], 200);
    }

    public function store(Request $request)
    {
        if ($request->post_id != "") {
            $admin = User::find($request->post_id);
            $request->validate(
                [
                    "name" => "required|min:2|max:255",
                    "email" => $admin->email != $request->email ? "required|max:40|email:rfc,dns|without_spaces|unique:users|unique:customers" : "required|max:40|email:rfc,dns|without_spaces|unique:customers",
                    "password" =>  $request->password != null || $request->password != "" ? "required|min:8|max:20|confirmed" : "",
                ]
            );
            $user = User::updateOrCreate(['id' => $request->post_id], [
                "name" => $request->name,
                "email" => $request->email,
                "password" => $request->password != null || $request->password != "" ? bcrypt($request->password) : $admin->password,

            ]);
        } else {
            //เพิ่มข้อมูลใหม่
            $request->validate(
                [
                    "type" => "required",
                    "text" => $request->type == 1 ? "required|max:1000" : "",
                    "image" => $request->type == 0 ? "required|mimes:png,jpg,jpeg" : "",
                    // "telephone" => "required|numeric|digits:10|unique:users|unique:admins",
                    // "credit" => "required|numeric",
                    // "share_percentage" => 'required|numeric|between:0,99.99',
                ],
            );
            $full_path = '';
            if ($request->type == 0) {
                //การเข้ารหัสรูปภาพ
                $service_image = $request->file('image');
                //genarate ชื่อภาพ
                $name_gen = hexdec(uniqid());
                //ดึงนามสกุลรูป
                $img_ext = strtolower($service_image->getClientOriginalExtension());
                $img_name = $name_gen . '.' . $img_ext;

                $upload_location = "service/images/";
                $full_path = $upload_location . $img_name;
                $service_image->move($upload_location, $img_name);
            }


            $user = Message::updateOrCreate(['id' => $request->post_id], [
                "type" => $request->type,
                "data" => $request->type == 1 ? $request->text : $full_path,
            ]);
        }
        if ($request->type == 0) $user->data2 = asset($user->data);
        $user->created_at_2 = Carbon::parse($user->created_at)->locale('th')->diffForHumans();
        return response()->json(['code' => '200', 'message' => 'บันทึกข้อมูลสำเร็จ', 'data' => $user], 200);
    }
}

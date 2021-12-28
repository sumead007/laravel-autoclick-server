<?php

namespace App\Http\Controllers\Line\SelectUserSent;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\Line;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SelectUserSentController extends Controller
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
        $datas = Line::orderBy('status', 'asc')->paginate(100);

        return view('line.select_user_sent.home', compact('datas'));
    }

    public function store(Request $request)
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
            Line::find($request->select[$i])->update(["status" => $request->status]);
        }

        return response()->json(['code' => '200', 'message' => 'บันทึกข้อมูลสำเร็จ', 'data' => ["id" => $request->select, "status" => $request->status]], 200);
    }
}

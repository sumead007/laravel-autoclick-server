<?php

namespace App\Imports;

use App\Models\Line;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class LineImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        //    return dd($rows);
        foreach ($rows as $key => $row) {
            #ข้ามคอลัมแรก
            if ($key == 0) {
                continue;
            }
            #ลบช่องว่าง
            if ($row->filter()->isNotEmpty()) {
                $user_id = trim($row[1]);
                $user_tel = trim($row[0]);
                $new_user_tel = $user_tel[0] != "0" ? "0" . $user_tel : $user_tel;
                #ไม่เพิ่มอันซํ้า
                $chk_user_id = Line::where(["user_id" => $user_id])->get();
                $chk_user_tel = Line::where(["user_tel" => $new_user_tel])->get();
                if (count($chk_user_id) <= 0  && count($chk_user_tel) <= 0) {
                    $type = 0;
                    if ($new_user_tel == null) {
                        $type = 0;
                    }
                    if ($user_id == null) {
                        $type = 1;
                    }

                    Line::updateOrCreate(
                        ['id' => ""],
                        [
                            "user_id" => $user_id,
                            "user_tel" => $new_user_tel,
                            "status" => 0,
                            "type" => $type,
                        ]
                    );
                }
            }
        }
        return null;
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddColumnActionTbConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->integer('action')->after('image_screen_shot')->comment = "0= เริ่มต้นหรือรอล็อกอิน , 1= otp, 2= เริ่มแอต, 3=จบการทำงาน, 4=ล็อกอินไม่สำเร็จ";
            
        });
        
        DB::table('configs')->where('id',1)->update(
            array(
                'action' => 0,
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

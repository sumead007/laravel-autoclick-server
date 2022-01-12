<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOtpChatMachTbLineLogins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('line_logins', function (Blueprint $table) {
            $table->integer('num_add')->after('status')->default(0);
            $table->integer('otp')->after('num_add')->default(0)->comment = "0=รอการแอต(เริ่มต้น) 1=รอการแอต(ล็อก) 2=ยืนยันสำเร็จ";
            $table->integer('num_chat')->after('otp')->default(0);
            $table->string('machine')->after('num_chat')->nullable();
        });

        Schema::table('lines', function (Blueprint $table) {
            $table->integer('available')->after('status')->default(0)->comment = "(ไลน์นี้ใช้ได้มั้ย) 0=เริ่มต้น 1=ใช้ได้ 2=ใช้ไม่ได้";
            $table->integer('sent_success')->after('available')->default(0)->comment = "(ส่งสำเร็จมั้ย) 0=ยังไม่ส่ง 1=ส่งสำเร็จ 2=ส่งไม่สำเร็จ";
        });
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

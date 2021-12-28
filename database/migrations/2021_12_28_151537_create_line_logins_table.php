<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_logins', function (Blueprint $table) {
            $table->id();
            $table->string('user_login');
            $table->string('password');
            $table->integer('status')->comment = '0=ไม่ถูกใช้งาน 1=กำลังใช้งาน 2=โดนบล็อก';
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_logins');
    }
}

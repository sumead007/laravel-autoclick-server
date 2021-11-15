<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('user_login');
            $table->integer('type')->comment = '0=เบอร์โทร 1=อีเมล';
            $table->string('password');
            $table->integer('status')->comment = '0=ปิด 1=เปิด';
            $table->timestamps();
        });
        // Insert some data
        DB::table('configs')->insert(
            array(
                'user_login' => 'admin111',
                'password' => '12345678', ///แก้เข้ารหัส
                'type' => 0,
                'status' => 1
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
        Schema::dropIfExists('configs');
    }
}

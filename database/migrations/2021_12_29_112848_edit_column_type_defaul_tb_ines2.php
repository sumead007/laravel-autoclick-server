<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditColumnTypeDefaulTbInes2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->string('status')->default('0')->change();
        });

        Schema::table('lines', function (Blueprint $table) {
            $table->string('type')->default('1')->change();
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

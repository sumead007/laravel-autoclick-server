<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddColumnTotalQueueNumQueueTbConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->integer('queue_total')->after('action')->comment = "จำนวนที่จองไว้ทั้งหมด";
            $table->integer('queue_num')->after('queue_total')->comment = "ทำไปกี่คนแล้ว";
        });

        DB::table('configs')->where('id', 1)->update(
            array(
                'queue_total' => 0,
                'queue_num' => 0,
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

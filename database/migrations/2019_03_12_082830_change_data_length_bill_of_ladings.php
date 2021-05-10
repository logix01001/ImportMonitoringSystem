<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDataLengthBillOfLadings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_ladings', function (Blueprint $table) {
            //
            $table->string('factory', 30)->change();
            $table->string('bl_no', 50)->change();
            $table->string('supplier', 100)->change();
            $table->string('vessel', 100)->change();
            $table->string('connecting_vessel', 100)->change();
            $table->string('shipping_line', 100)->change();
            $table->string('forwarder', 100)->change();
            $table->string('broker', 100)->change();
            $table->string('pol', 50)->change();
            $table->string('country', 50)->change();
            $table->string('pod', 50)->change();
            $table->string('volume', 50)->change();
            $table->string('shipping_docs', 50)->change();
            $table->string('processing_date', 50)->change();
            $table->string('estimated_time_departure', 50)->change();
            $table->string('estimated_time_arrival', 50)->change();
            $table->string('actual_time_arrival', 50)->change();
            $table->string('date_endorse', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_ladings', function (Blueprint $table) {
            //
        });
    }
}

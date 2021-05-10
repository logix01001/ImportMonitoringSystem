<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTruckerBookingEtc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('containers', function (Blueprint $table) {
            //
            $table->string('trucker')->nullable();
            $table->string('booking_time')->nullable();
            $table->string('pull_out')->nullable();
            $table->string('unload')->nullable();
            $table->string('reason_of_delay_delivery')->nullable();
            $table->string('return_cy')->nullable();
            $table->string('return_date')->nullable();
            $table->integer('box_number')->default(0);
            $table->integer('summary_number')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('containers', function (Blueprint $table) {
            //
            $table->dropColumn('trucker');
            $table->dropColumn('booking_time');
            $table->dropColumn('pull_out');
            $table->dropColumn('unload');
            $table->dropColumn('reason_of_delay_delivery');
            $table->dropColumn('return_cy');
            $table->dropColumn('return_date');
            $table->dropColumn('box_number');
            $table->dropColumn('summary_number');
        });
    }
}

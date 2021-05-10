<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShipmentsOnProcess extends Migration
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
            $table->string('sop_current_status')->nullable();
            $table->string('sop_remarks')->nullable();
            $table->string('target_gatepass')->nullable();
            $table->string('actual_gatepass')->nullable();
            $table->string('reason_of_delay_gatepass')->nullable();

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
            $table->dropColumn('sop_current_status');
            $table->dropColumn('sop_remarks');
            $table->dropColumn('target_gatepass');
            $table->dropColumn('actual_gatepass');
            $table->dropColumn('reason_of_delay_gatepass');
        });
    }
}

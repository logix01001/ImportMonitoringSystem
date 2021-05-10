<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveGatepassInBillOfLading extends Migration
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
            $table->dropColumn('sop_current_status');
            $table->dropColumn('actual_gatepass');
            $table->dropColumn('reason_of_delay_gatepass');
            $table->dropColumn('sop_remarks');
            
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
            $table->string('sop_remarks')->nullable();
            $table->string('sop_current_status')->nullable();
            $table->string('actual_gatepass')->nullable();
            $table->string('reason_of_delay_gatepass')->nullable();
        });
    }
}

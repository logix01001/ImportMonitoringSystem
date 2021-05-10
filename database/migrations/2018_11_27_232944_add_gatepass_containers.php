<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGatepassContainers extends Migration
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
            $table->string('sop_current_status')->nullable();
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
        Schema::table('containers', function (Blueprint $table) {
            //
            $table->dropColumn('sop_current_status');
            $table->dropColumn('actual_gatepass');
            $table->dropColumn('reason_of_delay_gatepass');
        });
    }
}

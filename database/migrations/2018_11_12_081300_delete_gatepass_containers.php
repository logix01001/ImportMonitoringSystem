<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteGatepassContainers extends Migration
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
            $table->dropColumn('target_gatepass');
            $table->dropColumn('actual_gatepass');
            $table->dropColumn('reason_delay_gatepass');
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
            $table->string('target_gatepass')->nullable();
            $table->string('actual_gatepass')->nullable();
            $table->string('reason_delay_gatepass')->nullable();
        });
    }
}

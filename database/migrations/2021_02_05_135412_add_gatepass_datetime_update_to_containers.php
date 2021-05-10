<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGatepassDatetimeUpdateToContainers extends Migration
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
            $table->string('gatepass_datetime_update',50)->after('actual_gatepass')->nullable();
            $table->string('gatepass_update_by',50)->after('gatepass_datetime_update')->nullable();

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
            $table->dropColumn('gatepass_datetime_update');
            $table->dropColumn('gatepass_update_by');
        });
    }
}

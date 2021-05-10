<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexBlNumberChange255Blcontainers extends Migration
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
			$table->index('bl_no_fk');
			$table->index('container_number');
			$table->string('bl_no_fk',50)->change();
			$table->string('container_number',50)->change();
			$table->string('actual_discharge',50)->nullable()->change();
			$table->string('dismounted_cy',50)->nullable()->change();
			$table->string('dismounted_date',50)->nullable()->change();
			$table->string('split_remarks',100)->nullable()->change();
			$table->string('container_type',20)->nullable()->change();
			$table->string('split_bl_no_fk',100)->nullable()->change();
			$table->string('validity_storage',50)->nullable()->change();
			$table->string('validity_demurrage',50)->nullable()->change();
			$table->string('revalidity_storage',50)->nullable()->change();
			$table->string('revalidity_demurrage',50)->nullable()->change();
			$table->string('revalidity_remarks',100)->nullable()->change();
			$table->string('trucker',50)->nullable()->change();
			$table->string('pull_out_time',50)->nullable()->change();
			$table->string('pull_out',50)->nullable()->change();
			$table->string('unload',50)->nullable()->change();
			//$table->string('reason_of_delay_delivery',100)->nullable()->change();
			$table->string('return_cy',50)->nullable()->change();
			$table->string('return_date',50)->nullable()->change();
			$table->string('safe_keep',50)->nullable()->change();
			//$table->string('sop_current_status',100)->nullable()->change();
			$table->string('actual_gatepass',50)->nullable()->change();
			//$table->string('reason_of_delay_gatepass',100)->nullable()->change();
			$table->string('initial_demurrage',50)->nullable()->change();
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
			$table->dropIndex('containers_bl_no_fk_index');
			$table->dropIndex('containers_container_number_index');
			$table->string('bl_no_fk')->change();
			$table->string('container_number')->change();
			$table->string('actual_discharge')->nullable()->change();
			$table->string('dismounted_cy')->nullable()->change();
			$table->string('dismounted_date')->nullable()->change();
			$table->string('split_remarks')->nullable()->change();
			$table->string('container_type')->nullable()->change();
			$table->string('split_bl_no_fk')->nullable()->change();
			$table->string('validity_storage')->nullable()->change();
			$table->string('validity_demurrage')->nullable()->change();
			$table->string('revalidity_storage')->nullable()->change();
			$table->string('revalidity_demurrage')->nullable()->change();
			$table->string('revalidity_remarks')->nullable()->change();
			$table->string('trucker')->nullable()->change();
			$table->string('pull_out_time')->nullable()->change();
			$table->string('pull_out')->nullable()->change();
			$table->string('unload')->nullable()->change();
			//$table->string('reason_of_delay_delivery')->nullable()->change();
			$table->string('return_cy')->nullable()->change();
			$table->string('return_date')->nullable()->change();
			$table->string('safe_keep')->nullable()->change();
			//$table->string('sop_current_status')->nullable()->change();
			$table->string('actual_gatepass')->nullable()->change();
			//$table->string('reason_of_delay_gatepass')->nullable()->change();
			$table->string('initial_demurrage')->nullable()->change();
        });
    }
}

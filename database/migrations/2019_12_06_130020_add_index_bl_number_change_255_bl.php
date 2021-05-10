<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexBlNumberChange255Bl extends Migration
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
			$table->index('bl_no');
			$table->string('place_endorsement',50)->nullable()->change();
			$table->string('actual_process',50)->nullable()->change();
			//$table->string('remarks_of_docs')->nullable()->change();
			$table->string('tsad_no',30)->nullable()->change();
			$table->string('xray',50)->nullable()->change();
			$table->string('actual_berthing_date',50)->nullable()->change();
			$table->string('target_gatepass',50)->nullable()->change();
			$table->string('date_approve_ip',50)->nullable()->change();
			$table->string('e2m',50)->nullable()->change();
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
			$table->dropIndex('bill_of_ladings_bl_no_index');
			$table->string('place_endorsement')->nullable()->change();
			$table->string('actual_process')->nullable()->change();
			//$table->string('remarks_of_docs')->nullable()->change();
			$table->string('tsad_no')->nullable()->change();
			$table->string('xray')->nullable()->change();
			$table->string('actual_berthing_date')->nullable()->change();
			$table->string('target_gatepass')->nullable()->change();
			$table->string('date_approve_ip')->nullable()->change();
			$table->string('e2m')->nullable()->change();
        });
    }
}

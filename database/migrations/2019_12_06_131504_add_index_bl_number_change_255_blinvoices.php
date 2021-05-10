<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexBlNumberChange255Blinvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_lading_invoices', function (Blueprint $table) {
            //
			$table->index('bl_no_fk');
			$table->string('bl_no_fk',50)->change();
			$table->string('invoice_number',100)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_lading_invoices', function (Blueprint $table) {
            //
			$table->dropIndex('bill_of_lading_invoices_bl_no_fk_index');
			$table->string('invoice_number')->change();
			$table->string('bl_no_fk')->change();
        });
    }
}

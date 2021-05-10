<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNotnullableFactory extends Migration
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
            $table->string('factory',30)->nullable(false)->change();
            $table->string('bl_no',50)->nullable(false)->change();
            $table->string('supplier',100)->nullable(false)->change();
            $table->string('shipping_line',100)->nullable(false)->change();
            //$table->string('vessel',100)->nullable(false)->change();
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
            $table->string('factory',30)->nullable()->change();
            $table->string('bl_no',50)->nullable()->change();
            $table->string('supplier',100)->nullable()->change();
            $table->string('shipping_line',100)->nullable()->change();
           // $table->string('vessel',100)->nullable()->change();
        });
    }
}

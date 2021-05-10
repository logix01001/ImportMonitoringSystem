<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBocFieldsInBl extends Migration
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

            $table->string('xray')->nullable();
            $table->string('commodity_for_boc')->nullable();
            
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

            $table->dropColumn('xray');
            $table->dropColumn('commodity_for_boc');

        });
    }
}

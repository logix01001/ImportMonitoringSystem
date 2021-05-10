<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexEstimatedTimeArrival extends Migration
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
            $table->index('estimated_time_arrival');
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
            $table->dropIndex('bill_of_ladings_estimated_time_arrival_index');
        });
    }
}

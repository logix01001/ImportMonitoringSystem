<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBoxSummaryNumberPullout extends Migration
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
            $table->integer('pull_out_box_number')->default(0);
            $table->integer('pull_out_summary_number')->default(0);
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
            $table->dropColumn('pull_out_box_number');
            $table->dropColumn('pull_out_summary_number');
        });
    }
}

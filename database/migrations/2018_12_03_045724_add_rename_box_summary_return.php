<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRenameBoxSummaryReturn extends Migration
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
            $table->renameColumn('box_number', 'return_box_number');
            $table->renameColumn('summary_number', 'return_summary_number');
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
            $table->renameColumn('return_box_number', 'box_number');
            $table->renameColumn('return_summary_number', 'summary_number');
        });
    }
}

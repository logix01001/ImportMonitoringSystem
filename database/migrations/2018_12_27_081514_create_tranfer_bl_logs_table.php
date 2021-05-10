<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranferBlLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tranfer_bl_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('old_bl_number',30);
            $table->string('new_bl_number',30);
            $table->string('edited_by',30);
            $table->string('cpu_used',100);
            $table->string('date_edited',50);
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tranfer_bl_logs');
    }
}

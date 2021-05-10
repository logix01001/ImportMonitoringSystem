<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContainerTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',30);
            $table->string('added_by')->nullable();
            $table->string('added_cpu_used')->nullable();
            $table->ipAddress('added_ip_used')->nullable();
            $table->softDeletes();
            $table->string('deleted_by')->nullable();
            $table->string('deleted_cpu_used')->nullable();
            $table->ipAddress('deleted_ip_used')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('container_types');
    }
}

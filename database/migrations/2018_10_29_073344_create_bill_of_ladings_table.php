<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillOfLadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_of_ladings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('factory')->nullable();
            $table->string('bl_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('supplier')->nullable();
            $table->string('commodity')->nullable();
            $table->string('vessel')->nullable();
            $table->string('connecting_vessel')->nullable();
            $table->string('shipping_line')->nullable();
            $table->string('forwarder')->nullable();
            $table->string('broker')->nullable();
            $table->string('pol')->nullable();
            $table->string('country')->nullable();
            $table->string('pod')->nullable();
            $table->string('volume')->nullable();
            $table->string('shipping_docs')->nullable();
            $table->string('processing_date')->nullable();
            $table->string('estimated_time_departure')->nullable();
            $table->string('estimated_time_arrival')->nullable();
            $table->string('actual_time_arrival')->nullable();
            $table->string('date_endorse')->nullable();
            $table->string('endorser')->nullable();
            $table->string('actual_process')->nullable();
            $table->string('remarks_of_docs')->nullable();
            $table->string('tsad_no')->nullable();
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
        Schema::dropIfExists('bill_of_ladings');
    }
}

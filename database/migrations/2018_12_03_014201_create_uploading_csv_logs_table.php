<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadingCsvLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploading_csv_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uploaded_filed');
            $table->string('upload_category');
            $table->string('uploaded_by');
            $table->timestamp('uploaded_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploading_csv_logs');
    }
}

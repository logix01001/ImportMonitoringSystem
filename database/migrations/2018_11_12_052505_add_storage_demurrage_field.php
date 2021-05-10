<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStorageDemurrageField extends Migration
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
            $table->boolean('xray')->default(false);
            $table->string('validity_storage')->nullable();
            $table->string('validity_demurrage')->nullable();
            $table->string('revalidity_storage')->nullable();
            $table->string('revalidity_demurrage')->nullable();
            $table->string('revalidity_remarks')->nullable();
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
            $table->dropColunm('xray');
            $table->dropColunm('validity_storage');
            $table->dropColunm('validity_demurrage');
            $table->dropColunm('revalidity_storage');
            $table->dropColunm('revalidity_demurrage');
            $table->dropColunm('revalidity_demurrage');
            
        });
    }
}

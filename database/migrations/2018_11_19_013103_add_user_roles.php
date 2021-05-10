<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->boolean('master')->default(false);
            $table->boolean('encoding')->default(false);
            $table->boolean('arrival')->default(false);
            $table->boolean('e2m')->default(false);
            $table->boolean('gatepass')->default(false);
            $table->boolean('storage_validity')->default(false);
            $table->boolean('container_movement')->default(false);
            $table->boolean('safe_keep')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('master');
            $table->dropColumn('encoding');
            $table->dropColumn('arrival');
            $table->dropColumn('e2m');
            $table->dropColumn('gatepass');
            $table->dropColumn('storage_validity');
            $table->dropColumn('container_movement');
            $table->dropColumn('safe_keep');

        });
    }
}

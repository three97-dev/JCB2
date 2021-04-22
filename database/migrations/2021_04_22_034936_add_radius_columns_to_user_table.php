<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRadiusColumnsToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('primary_radius',50)->nullable();
            $table->string('secondary_radius',50)->nullable();
            $table->string('default_address',50)->default('primary');


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
            $table->dropColumn('primary_radius');
            $table->dropColumn('secondary_radius');
            $table->dropColumn('default_address');
        });
    }
}

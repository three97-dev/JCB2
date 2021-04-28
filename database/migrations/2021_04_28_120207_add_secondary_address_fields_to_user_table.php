<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecondaryAddressFieldsToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('secondary_street')->nullable();
            $table->string('secondary_city')->nullable();
            $table->string('secondary_state')->nullable();
            $table->string('secondary_code')->nullable();
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
            $table->dropColumn('secondary_street');
            $table->dropColumn('secondary_city');
            $table->dropColumn('secondary_state');
            $table->dropColumn('secondary_code');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSecondaryAddressFromUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('billing_suite');
            $table->dropColumn('billing_name');
            $table->dropColumn('secondary_street');           
            $table->dropColumn('secondary_city');
            $table->dropColumn('secondary_state');
            $table->dropColumn('secondary_code');
            $table->dropColumn('distance');
            $table->dropColumn('secondary_radius');
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
            $table->string('billing_suite');
            $table->string('billing_name');
            $table->string('secondary_state');
            $table->string('secondary_code');
            $table->string('secondary_street');
            $table->string('secondary_city');
            $table->string('distance');
            $table->string('secondary_radius');
        });
    }
} 

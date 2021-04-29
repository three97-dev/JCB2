<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {

            $table->id();
            $table->string('user_id');
            $table->string('tab_id');
            $table->string('secondary_street')->nullable();
            $table->string('secondary_city')->nullable();
            $table->string('secondary_state')->nullable();
            $table->string('secondary_code')->nullable();
            $table->string('billing_suite')->nullable();
            $table->string('billing_name')->nullable();
            $table->string('secondary_radius')->nullable();

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
        Schema::dropIfExists('addresses');
    }
}

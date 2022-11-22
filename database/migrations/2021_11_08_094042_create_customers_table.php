<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->string('image')->nullable();
            $table->string('state')->nullable();
            $table->integer('loyalty_points')->nullable();
            $table->integer('is_loyalty_enrolled')->nullable();
            $table->timestamp('loyalty_expire_date')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('address')->nullable();
            $table->double('balance')->nullable();
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
        Schema::dropIfExists('customers');
    }
}

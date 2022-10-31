<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesaCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mpesa_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id');
            $table->string('consumer_key');
            $table->string('consumer_secret');
            $table->string('test_consumer_key');
            $table->string('test_consumer_secret');
            $table->string('environment');
            $table->string('shortcode');
            $table->text('security_credential');
            $table->string('lipa_na_mpesa_passkey');
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
        Schema::dropIfExists('mpesa_credentials');
    }
}

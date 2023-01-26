<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->foreignId('referrer_id')->nullable();
            $table->foreignId('referred_id')->nullable();
            $table->string('referrer_kickback')->nullable();
            $table->string('referred_kickback')->nullable();
            $table->string('prefered_way_to_be_contacted')->enum(['sms','email','whatsapp','call']);
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
        Schema::dropIfExists('referrals');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_returns', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->double('order_amount')->default(0);
            $table->double('total_tax');
            $table->double('collected_cash')->nullable();
            $table->double('extra_discount')->nullable();
            $table->string('coupon_code')->nullable();
            $table->double('coupon_discount_amount')->default(0);
            $table->string('coupon_discount_title')->nullable();
            $table->foreignId('payment_id')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
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
        Schema::dropIfExists('order_returns');
    }
}

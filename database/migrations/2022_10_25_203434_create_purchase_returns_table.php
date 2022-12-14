<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->bigInteger('supplier_id')->nullable();
            $table->bigInteger('company_id')->nullable();
            $table->double('order_amount')->default(0);
            $table->double('total_tax');
            $table->double('collected_cash')->nullable();
            $table->double('extra_discount')->nullable();
            $table->string('coupon_code')->nullable();
            $table->double('coupon_discount_amount')->default(0);
            $table->string('coupon_discount_title')->nullable();
            $table->foreignId('payment_id')->nullable();
            $table->string('transaction_reference')->nullable();
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
        Schema::dropIfExists('purchase_returns');
    }
}

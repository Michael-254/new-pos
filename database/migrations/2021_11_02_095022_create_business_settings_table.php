<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id');
            $table->text('shop_logo')->nullable();
            $table->text('pagination_limit')->nullable();
            $table->text('currency')->nullable();
            $table->text('shop_name')->nullable();
            $table->text('shop_address')->nullable();
            $table->text('shop_phone')->nullable();
            $table->text('shop_email')->nullable();
            $table->text('footer_text')->nullable();
            $table->text('country')->nullable();
            $table->text('stock_limit')->nullable();
            $table->text('time_zone')->nullable();
            $table->text('vat_reg_no')->nullable();
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
        Schema::dropIfExists('business_settings');
    }
}

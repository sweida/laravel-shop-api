<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_goods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id');
            $table->unsignedInteger('good_id');
            $table->string('good_name');
            $table->string('label')->nullable();        // 规格
            $table->integer('label_id')->nullable()->default(1);        // 规格
            $table->integer('price');       // 价格，考虑到价格会变动，所以保存购买时的价格
            $table->integer('count');       // 数量

            // $table->foreign('order_id')->references('id')->on('orders');
            // $table->foreign('good_id')->references('id')->on('goods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_goods');
    }
}

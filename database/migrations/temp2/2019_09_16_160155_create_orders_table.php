<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('orderId')->unique();
            $table->string('state');
            $table->integer('totalPrice');
            $table->string('expressType');      // 物流方式
            $table->string('expressName');      // 物流名称
            $table->integer('expressPrice');      // 物流金额
            $table->integer('discount')->default(0);        // 优惠金额
            // 需要订单详情，购买的商品列表
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
        Schema::dropIfExists('orders');
    }
}

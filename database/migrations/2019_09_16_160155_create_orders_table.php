<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     * 订单  
     * 退货逻辑
     * 1。退货申请， 2.退货申请通过 3.退货申请拒绝 4.退货发货中 5.退货中 5.退货成功
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id')->unique();
            $table->string('user_id');
            $table->string('status')->default(1);              // 订单状态 1.待付款 2.已付款，待发货 3.已发货 4.已签收，待确定 5.完成 6.取消订单 7.支付超时，已取消订单  
            $table->integer('goodPrice');                      // 商品总价
            $table->integer('totalPay');                       // 总共应付
            $table->string('expressType');                     // 物流方式
            $table->string('expressName');                     // 物流名称
            $table->integer('expressPrice');                   // 物流金额
            $table->string('addressName');                     // 配送姓名
            $table->string('addressPhone');                    // 配送电话
            $table->string('address');                         // 配送地址
            $table->integer('discount')->default(0);           // 优惠金额
            $table->integer('discount_id')->nullable();        // 优惠券
            // 需要订单详情，购买的商品列表 order-good 表
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

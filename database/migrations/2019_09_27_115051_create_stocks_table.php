<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     * 商品规格
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('good_id');
            $table->integer('stock')->nullbale()->default(9999);    // 库存
            $table->string('label')->nullbale();        // 规格
            $table->integer('label_id');        // 规格编号
            $table->integer('price');       // 价格
            $table->integer('vip_price')->nullbale();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}

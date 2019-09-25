<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->integer('price');
            $table->integer('vipprice')->nullable();        // 会员价格
            $table->string('classify')->nullable();     // 分类
            $table->string('desc')->nullable();     // 描述
            $table->string('detail')->nullable();   // 详情
            $table->integer('stock')->nullable();               // 库存
            $table->string('parameter')->nullable();    // 规格
            $table->integer('clicks')->nullable()->default(0);      // 浏览量
            $table->integer('likes')->nullable()->default(0);      // 收藏量
            $table->integer('buys')->nullable()->default(0);      // 购买量
            $table->softDeletes();
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
        Schema::dropIfExists('goods');
    }
}

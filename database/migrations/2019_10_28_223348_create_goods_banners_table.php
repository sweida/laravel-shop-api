<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('goods_id');
            $table->string('url');
            $table->integer('number')->nullable();
            $table->string('active')->nullable();       // 是否当封面
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_banners');
    }
}

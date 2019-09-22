<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodbannersTable extends Migration
{
    /**
     * Run the migrations.
     *  商品banner图
     * @return void
     */
    public function up()
    {
        Schema::create('goodbanners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('good_id');
            $table->string('url');
            $table->string('active')->nullable();       // 是否当封面
            $table->timestamps();

            $table->foreign('good_id')->references('id')->on('goods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goodbanners');
    }
}

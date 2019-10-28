<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     * 用户表
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid')->unique();
            $table->string('nickName');
            $table->text('avatarUrl')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('session_key')->nullable();
            $table->timestamps();
            // unique 唯一
            // nullable 可以为空
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

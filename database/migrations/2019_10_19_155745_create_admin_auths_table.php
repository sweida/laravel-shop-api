<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminAuthsTable extends Migration
{
    /**
     * Run the migrations.
     * 多账号登录
     * @return void
     */
    public function up()
    {
        Schema::create('admin_auths', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('identity_type');                    // 登录类型
            $table->string('identifier');                       // 登录号
            $table->string('password');                         // 密码
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
        Schema::dropIfExists('admin_auths');
    }
}

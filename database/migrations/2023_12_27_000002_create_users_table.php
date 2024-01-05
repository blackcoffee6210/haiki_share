<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('group');                                //ユーザーを判別(1なら利用者、2ならコンビニ側)
            $table->string('name');                                  //名前
            $table->string('branch')->nullable();                    //支店名
            $table->unsignedBigInteger('prefecture_id')->nullable(); //都道府県id
            $table->string('address')->nullable();                   //住所
            $table->string('email')->unique();                       //Emailアドレス
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');                              //パスワード
            $table->rememberToken();                                        //リメンバートークン
            $table->string('image')->nullable();                    //ユーザー画像
            $table->string('introduce')->nullable();                //自己紹介文
            $table->softDeletes();
            $table->timestamps();

	        $table->foreign('prefecture_id')->references('id')->on('prefectures'); //外部キー
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

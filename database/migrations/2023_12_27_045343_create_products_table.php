<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');               //商品ID
            $table->unsignedBigInteger('user_id');     //ユーザーID
            $table->unsignedBigInteger('category_id'); //カテゴリーID
            $table->string('name');                    //商品名
            $table->string('image')->nullable();       //商品画像
            $table->string('detail');                  //商品の内容
            $table->integer('price');                  //金額
            $table->dateTime('expire');                //賞味期限
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');          //外部キー
	        $table->foreign('category_id')->references('id')->on('categories'); //外部キー

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}

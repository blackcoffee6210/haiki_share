<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCancelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancels', function (Blueprint $table) {
	        $table->bigIncrements('id');
	        $table->unsignedBigInteger('cancel_user_id'); //キャンセルしたユーザーid（利用者）
	        $table->unsignedBigInteger('post_user_id');   //出品したユーザーid（出品者）
	        $table->unsignedBigInteger('product_id');     //商品id
            $table->timestamps();

	        $table->foreign('cancel_user_id')->references('id')->on('users');       //外部キー
	        $table->foreign('post_user_id')->references('id')->on('users');       //外部キー
	        $table->foreign('product_id')->references('id')->on('products'); //外部キー
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cancels');
    }
}

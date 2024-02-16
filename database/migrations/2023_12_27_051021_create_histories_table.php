<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
	        $table->bigIncrements('id');              //購入履歴ID
            $table->unsignedBigInteger('buyer_id');   //購入したユーザーID（利用者）
            $table->unsignedBigInteger('seller_id');  //出品ユーザーID（コンビニ）
            $table->unsignedBigInteger('product_id'); //商品ID
            $table->timestamps();

	        $table->foreign('buyer_id')->references('id')->on('users');      //外部キー
	        $table->foreign('seller_id')->references('id')->on('users');     //外部キー
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
        Schema::dropIfExists('histories');
    }
}

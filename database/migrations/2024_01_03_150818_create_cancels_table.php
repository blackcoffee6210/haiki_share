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
	        $table->unsignedBigInteger('user_id');    //ユーザーid
	        $table->unsignedBigInteger('product_id'); //商品id
            $table->timestamps();

	        $table->foreign('user_id')->references('id')->on('users');       //外部キー
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

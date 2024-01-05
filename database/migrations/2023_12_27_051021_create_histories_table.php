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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');    //ユーザーid
            $table->unsignedBigInteger('product_id'); //商品id
            $table->softDeletes();
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
        Schema::dropIfExists('histories');
    }
}

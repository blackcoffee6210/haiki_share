<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sender_id');   //送信者のユーザーid
            $table->unsignedBigInteger('receiver_id'); //受信者のユーザーid
            $table->string('title');                   //レビュータイトル
            $table->integer('recommendation');         //おすすめかどうか
            $table->string('detail');                  //レビューの内容
            $table->softDeletes();
            $table->timestamps();

            //外部キー
	        $table->foreign('sender_id')->references('id')->on('users');
	        $table->foreign('receiver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}

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
            $table->unsignedBigInteger('sender_id');         //送信者のユーザーid
            $table->unsignedBigInteger('receiver_id');       //受信者のユーザーid
            $table->unsignedBigInteger('recommendation_id'); //おすすめかどうか
	        $table->string('title');                         //レビュータイトル
            $table->string('detail');                        //レビューの内容
            $table->timestamps();

	        $table->foreign('sender_id')->references('id')->on('users');                   //外部キー
	        $table->foreign('receiver_id')->references('id')->on('users');                 //外部キー
	        $table->foreign('recommendation_id')->references('id')->on('recommendations'); //外部キー
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

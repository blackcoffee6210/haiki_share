<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('branch');
            $table->unsignedBigInteger('prefecture_id');
            $table->string('address');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('introduce')->nullable();
            $table->softDeletes();
            $table->timestamps();

            //外部キー
	          $table->foreign('prefecture_id')->references('id')->on('prefectures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
}

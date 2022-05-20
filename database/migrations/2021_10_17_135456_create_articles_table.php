<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('body');
            $table->string('pic1')->nullable();
            // $table->string('pic2')->nullable();
            // $table->string('pic3')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('category_id')->constrained('categories');
            //$table->foreignId('user_id')->onDelete('cascade');
            //$table->foreignId('category_id')->onDelete('cascade');
            $table->timestamps();
              // 以下だとテストコードが通らないので注意
            // $table->foreignId('user_id')->constrained('users');
            // $table->foreignId('category_id')->constrained('categories');

            //$table->foreign('user_id')->references('id')->on('users');
            //$table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}

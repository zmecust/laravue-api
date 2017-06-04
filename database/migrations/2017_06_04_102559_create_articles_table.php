<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('title');
            $table->string('body');
            $table->string('image_url')->nullable()->comment('话题封面图片');
            $table->integer('user_id')->unsigned();
            $table->integer('category_id')->unsigned()->default(0)->index();
            $table->integer('view_count')->unsigned()->default(0)->index();
            $table->integer('comments_count')->unsigned()->default(0);
            $table->integer('followers_count')->unsigned()->default(1);
            $table->enum('close_comment', ['T', 'F'])->default('F')->index();
            $table->enum('is_hidden', ['T', 'F'])->default('F')->index();
            $table->enum('is_excellent', ['T', 'F'])->default('F')->index();
            $table->timestamp('last_comment_time');
            $table->timestamps();
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

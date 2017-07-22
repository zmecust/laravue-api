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
            $table->integer('user_id')->unsigned();
            $table->integer('last_comment_user_id')->unsigned()->default(0)->index()->comment('最后评论时间');
            $table->integer('category_id')->unsigned()->default(0)->index()->comment('所属类别');
            $table->integer('view_count')->unsigned()->default(0)->index()->comment('观看数');
            $table->integer('comments_count')->unsigned()->default(0)->comment('评论数');
            $table->enum('close_comment', ['T', 'F'])->default('F')->index()->comment('是否关闭评论');
            $table->enum('is_hidden', ['T', 'F'])->default('F')->index()->comment('是否隐藏');
            $table->enum('is_excellent', ['T', 'F'])->default('F')->index()->comment('是否为精华');
            $table->integer('order')->default(0)->index()->comment('排序');
            $table->timestamp('last_comment_time')->comment('最后评论时间');
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

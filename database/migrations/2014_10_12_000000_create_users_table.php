<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('avatar');
            $table->string('password');
            $table->string('real_name')->nullable();
            $table->string('city')->nullable();
            $table->integer('articles_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('followers_count')->default(0)->comment('被关注人数');
            $table->integer('followings_count')->default(0)->comment('关注他人数');
            $table->enum('is_banned', ['T', 'F'])->default('F')->index()->comment('是否禁止');
            $table->string('confirm_code', 60)->comment('激活码');
            $table->integer('is_confirmed')->default(0)->comment('是否激活了用户');
            $table->timestamp('last_actived_at')->nullable()->comment('最后访问时间');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_uid')->unsigned()->nullable()->default(0)->comment('发自');
            $table->integer('to_uid')->unsigned()->nullable()->default(0)->comment('发送至');
            $table->string('content', 1000)->nullable()->comment('内容');
            $table->boolean('is_read')->nullable()->default(0)->comment('0未读 1已读');
            $table->bigInteger('dialog_id')->nullable();
            $table->timestamp('read_at')->nullable()->comment('读取时间');
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
        Schema::dropIfExists('messages');
    }
}

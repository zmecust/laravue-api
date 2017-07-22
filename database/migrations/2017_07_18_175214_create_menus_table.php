<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('parent_id')->default(0)->comment('父级ID');
            $table->string('name', 50)->comment('前端路由名');
            $table->string('display_name', 50)->nullable()->comment('名称');
            $table->string('description')->nullable()->comment('详细描述');
            $table->unsignedTinyInteger('sort')->default(100)->comment('排序字段,越小越靠前');
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
        Schema::dropIfExists('menus');
    }
}

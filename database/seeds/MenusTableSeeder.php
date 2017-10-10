<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('menus')->delete();

        \DB::table('menus')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'parent_id' => 0,
                    'name' => '/parent/setting',
                    'display_name' => '系统管理',
                    'description' => NULL,
                    'sort' => 100,
                    'created_at' => '2017-05-03 05:30:26',
                    'updated_at' => '2017-05-03 05:36:15',
                ),
            1 =>
                array (
                    'id' => 2,
                    'parent_id' => 1,
                    'name' => '/menus/index',
                    'display_name' => '菜单列表',
                    'description' => '菜单列表',
                    'sort' => 1,
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            2 =>
                array (
                    'id' => 3,
                    'parent_id' => 1,
                    'name' => '/roles/index',
                    'display_name' => '角色列表',
                    'description' => '角色列表',
                    'sort' => 2,
                    'created_at' => '2017-05-03 05:31:36',
                    'updated_at' => '2017-05-03 05:31:36',
                ),
            3 =>
                array (
                    'id' => 4,
                    'parent_id' => 1,
                    'name' => '/users/index',
                    'display_name' => '用户列表',
                    'description' => '用户列表',
                    'sort' => 3,
                    'created_at' => '2017-05-03 05:32:32',
                    'updated_at' => '2017-05-03 05:32:32',
                ),
            4 =>
                array (
                    'id' => 5,
                    'parent_id' => 1,
                    'name' => '/permissions/index',
                    'display_name' => '权限列表',
                    'description' => '权限列表',
                    'sort' => 4,
                    'created_at' => '2017-05-03 05:32:58',
                    'updated_at' => '2017-05-03 05:32:58',
                ),
            5 =>
                array (
                    'id' => 6,
                    'parent_id' => 0,
                    'name' => '/parent/content',
                    'display_name' => '内容管理',
                    'description' => NULL,
                    'sort' => 100,
                    'created_at' => '2017-05-03 05:30:26',
                    'updated_at' => '2017-05-03 05:36:15',
                ),
            6 =>
                array (
                    'id' => 7,
                    'parent_id' => 6,
                    'name' => '/articles/index',
                    'display_name' => '文章列表',
                    'description' => NULL,
                    'sort' => 1,
                    'created_at' => '2017-05-03 05:30:26',
                    'updated_at' => '2017-05-03 05:36:15',
                ),
        ));


    }
}
<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('permissions')->delete();

        \DB::table('permissions')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'name' => 'parent.setting',
                    'display_name' => '系统管理',
                    'description' => '系统管理',
                    'uri' => '/parent/setting',
                    'created_at' => '2017-04-26 12:00:00',
                    'updated_at' => '2017-04-26 12:00:00',
                ),
            1 =>
                array (
                    'id' => 2,
                    'name' => 'users.menu',
                    'display_name' => '用户菜单',
                    'description' => '用户菜单',
                    'uri' => 'request',
                    'created_at' => '2017-04-26 12:00:00',
                    'updated_at' => '2017-04-26 12:00:00',
                ),
            2 =>
                array (
                    'id' => 3,
                    'name' => 'menus.index',
                    'display_name' => '菜单列表',
                    'description' => '菜单列表',
                    'uri' => '/menus/index',
                    'created_at' => '2017-04-26 12:00:00',
                    'updated_at' => '2017-04-26 12:00:00',
                ),
            3 =>
                array (
                    'id' => 4,
                    'name' => 'permissions.index',
                    'display_name' => '权限列表',
                    'description' => '权限列表',
                    'uri' => '/permissions/index',
                    'created_at' => '2017-05-02 21:56:19',
                    'updated_at' => '2017-05-02 21:56:22',
                ),
            4 =>
                array (
                    'id' => 5,
                    'name' => 'roles.index',
                    'display_name' => '角色列表',
                    'description' => '角色列表',
                    'uri' => '/roles/index',
                    'created_at' => '2017-05-02 21:56:33',
                    'updated_at' => '2017-05-02 21:56:29',
                ),
            5 =>
                array (
                    'id' => 6,
                    'name' => 'users.index',
                    'display_name' => '用户列表',
                    'description' => '用户列表',
                    'uri' => '/users/index',
                    'created_at' => '2017-05-03 04:08:06',
                    'updated_at' => '2017-05-03 04:08:06',
                ),
            6 =>
                array (
                    'id' => 7,
                    'name' => 'parent.content',
                    'display_name' => '内容管理',
                    'description' => '内容管理',
                    'uri' => '/parent/content',
                    'created_at' => '2017-05-03 04:08:06',
                    'updated_at' => '2017-05-03 04:08:06',
                ),
            7 =>
                array (
                    'id' => 8,
                    'name' => 'articles.index',
                    'display_name' => '文章列表',
                    'description' => '文章列表',
                    'uri' => '/articles/index',
                    'created_at' => '2017-05-03 04:08:06',
                    'updated_at' => '2017-05-03 04:08:06',
                ),
        ));


    }
}
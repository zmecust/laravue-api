<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('roles')->delete();

        \DB::table('roles')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'name' => 'admin',
                    'display_name' => 'admin',
                    'description' => '管理员',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-24 16:20:48',
                ),
            1 =>
                array (
                    'id' => 2,
                    'name' => 'VIP',
                    'display_name' => 'VIP',
                    'description' => 'VIP',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-24 16:20:48',
                ),
            2 =>
                array (
                    'id' => 3,
                    'name' => 'owner',
                    'display_name' => 'owner',
                    'description' => '普通用户',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-24 16:20:48',
                ),
        ));


    }
}
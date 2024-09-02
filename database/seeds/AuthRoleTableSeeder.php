<?php

use Illuminate\Database\Seeder;

class AuthRoleTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('auth_role')->delete();
        
        \DB::table('auth_role')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'DEVELOPER',
                'alias' => 'DEV',
                'home' => 1492,
                'color' => '333333',
                'description' => NULL,
                'properties' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'deleted_by' => NULL,
                'created_at' => '2018-06-19 10:43:42',
                'updated_at' => '2020-07-13 12:45:41',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 10,
                'name' => 'SUPER ADMIN',
                'alias' => 'SU',
                'home' => 1492,
                'color' => 'CC0000',
                'description' => NULL,
                'properties' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'deleted_by' => NULL,
                'created_at' => '2018-06-19 10:43:42',
                'updated_at' => '2020-07-13 12:45:36',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 100,
                'name' => 'ADMINISTRATOR',
                'alias' => 'ADM',
                'home' => 1492,
                'color' => 'CC0000',
                'description' => NULL,
                'properties' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'deleted_by' => NULL,
                'created_at' => '2018-06-19 10:43:42',
                'updated_at' => '2020-07-13 12:45:25',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 210,
                'name' => 'PROJECT MANAGER',
                'alias' => 'PM',
                'home' => 1492,
                'color' => '0090FF',
                'description' => NULL,
                'properties' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'deleted_by' => NULL,
                'created_at' => '2018-06-19 10:43:42',
                'updated_at' => '2020-07-13 12:44:58',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 220,
                'name' => 'NOC',
                'alias' => 'NOC',
                'home' => 1492,
                'color' => '0090FF',
                'description' => NULL,
                'properties' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'deleted_by' => NULL,
                'created_at' => '2018-06-19 10:43:42',
                'updated_at' => '2020-07-13 12:45:02',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 1100,
                'name' => 'VENDOR',
                'alias' => 'VEND',
                'home' => 1492,
                'color' => '47e812',
                'description' => NULL,
                'properties' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'deleted_by' => NULL,
                'created_at' => '2018-06-19 10:43:42',
                'updated_at' => '2020-07-13 12:44:52',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 1110,
                'name' => 'FIELDTECH',
                'alias' => 'FTCH',
                'home' => 1492,
                'color' => '47e812',
                'description' => NULL,
                'properties' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'deleted_by' => NULL,
                'created_at' => '2018-06-19 10:43:42',
                'updated_at' => '2020-07-13 12:45:07',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 1200,
                'name' => 'CLIENT',
                'alias' => 'CLIENT',
                'home' => 1492,
                'color' => 'ffd400',
                'description' => NULL,
                'properties' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'deleted_by' => NULL,
                'created_at' => '2018-06-19 10:43:42',
                'updated_at' => '2020-07-13 12:45:11',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}
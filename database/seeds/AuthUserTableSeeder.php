<?php

use Illuminate\Database\Seeder;

class AuthUserTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('auth_user')->delete();
        
        \DB::table('auth_user')->insert(array (
            0 => 
            array (
                'id' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'role_id' => 1,
                'vendor_id' => NULL,
                'client_id' => NULL,
                'fieldtech_id' => NULL,
                'username' => 'devel',
                'email' => NULL,
                'password' => '$2y$10$NCy40Xate0Mpb7YHB5lajugHtZH573dTcY1kELOn1LCm8D/h5xVM.',
                'name' => 'Developers',
                'phone' => NULL,
                'photo' => NULL,
                'description' => NULL,
                'remember_token' => NULL,
                'last_ip' => NULL,
                'last_module' => NULL,
                'last_url' => NULL,
                'last_active' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'deleted_by' => NULL,
                'created_at' => '2018-06-19 10:38:52',
                'updated_at' => '2020-07-24 06:13:16',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => '4bee41d0-9b2a-4f64-bf75-b9eafdba9c9e',
                'role_id' => 10,
                'vendor_id' => NULL,
                'client_id' => NULL,
                'fieldtech_id' => NULL,
                'username' => 'admin',
                'email' => NULL,
                'password' => '$2y$10$e/djBZTVc6MfEQ3Gk/drJ.Pb5tkQgP3tZ6VywCtOJH7M1O5Zn.gZO',
                'name' => 'Administrator',
                'phone' => NULL,
                'photo' => NULL,
                'description' => NULL,
                'remember_token' => NULL,
                'last_ip' => NULL,
                'last_module' => NULL,
                'last_url' => NULL,
                'last_active' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '4bee41d0-9b2a-4f64-bf75-b9eafdba9c9e',
                'deleted_by' => NULL,
                'created_at' => '2020-03-26 02:44:22',
                'updated_at' => '2020-07-21 07:11:38',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => '9cf9e9a4-b64a-4c90-8e2e-167d7ede5d43',
                'role_id' => 210,
                'vendor_id' => NULL,
                'client_id' => NULL,
                'fieldtech_id' => NULL,
                'username' => 'user-pm',
                'email' => NULL,
                'password' => '$2y$10$GI8y8uc2eGNckgYoCNIbn.XYSYVtMJU5ghPOoLzVtksXs7oncS4FW',
                'name' => 'Project Manager',
                'phone' => NULL,
                'photo' => NULL,
                'description' => NULL,
                'remember_token' => NULL,
                'last_ip' => NULL,
                'last_module' => NULL,
                'last_url' => NULL,
                'last_active' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '9cf9e9a4-b64a-4c90-8e2e-167d7ede5d43',
                'deleted_by' => NULL,
                'created_at' => '2020-06-23 16:40:41',
                'updated_at' => '2020-07-21 07:38:20',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 'd6ab3259-9bc9-45a9-b86d-7f6c64866df9',
                'role_id' => 220,
                'vendor_id' => NULL,
                'client_id' => NULL,
                'fieldtech_id' => NULL,
                'username' => 'user-noc',
                'email' => NULL,
                'password' => '$2y$10$9Y7VT5kjHtYuTDHy8MXO2uNcs9MQZBcz8ge6KW6FMhIfvQdAmk/kG',
                'name' => 'User NOC',
                'phone' => NULL,
                'photo' => NULL,
                'description' => NULL,
                'remember_token' => NULL,
                'last_ip' => NULL,
                'last_module' => NULL,
                'last_url' => NULL,
                'last_active' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => 'd6ab3259-9bc9-45a9-b86d-7f6c64866df9',
                'deleted_by' => NULL,
                'created_at' => '2020-06-23 16:40:59',
                'updated_at' => '2020-07-02 22:00:53',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 'f2106abd-862c-4f08-a8cb-daaae7d789ec',
                'role_id' => 100,
                'vendor_id' => NULL,
                'client_id' => NULL,
                'fieldtech_id' => NULL,
                'username' => 'administrator',
                'email' => NULL,
                'password' => '$2y$10$lWEcmB3okdUuaD5yhApXSOG0Z53.7lo9nYFBMIwyvPWokD9ci/Ys2',
                'name' => 'Administrator',
                'phone' => NULL,
                'photo' => NULL,
                'description' => NULL,
                'remember_token' => NULL,
                'last_ip' => NULL,
                'last_module' => NULL,
                'last_url' => NULL,
                'last_active' => NULL,
                'created_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'updated_by' => '394c94ca-d220-4799-83a8-d6ccafc0b1af',
                'deleted_by' => NULL,
                'created_at' => '2020-06-23 16:40:07',
                'updated_at' => '2020-06-23 16:40:07',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}
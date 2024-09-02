<?php

use Illuminate\Database\Seeder;

class PoMClientTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('po_m_client')->delete();
        
        \DB::table('po_m_client')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'PT - A1',
                'alias' => 'A1',
                'address' => 'Bandung',
                'phone' => '0811234567',
                'email' => 'a1@gmail.com',
                'description' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'PT - A2',
                'alias' => 'A2',
                'address' => 'Jakarta',
                'phone' => '0817654321',
                'email' => 'a2@gmail.com',
                'description' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}
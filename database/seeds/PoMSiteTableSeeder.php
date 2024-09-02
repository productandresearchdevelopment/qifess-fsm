<?php

use Illuminate\Database\Seeder;

class PoMSiteTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('po_m_site')->delete();
        
        \DB::table('po_m_site')->insert(array (
            0 => 
            array (
                'id' => 2,
                'client_id' => 1,
                'name' => 'Location A1 - 1',
                'address' => 'Bandung',
                'pic' => 'Tan Hong Kiem',
                'lat' => NULL,
                'long' => NULL,
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
                'id' => 3,
                'client_id' => 1,
                'name' => 'Location A1 - 2',
                'address' => 'Bandung',
                'pic' => 'Tan Loo Mei',
                'lat' => NULL,
                'long' => NULL,
                'description' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 4,
                'client_id' => 1,
                'name' => 'Location A1 - 3',
                'address' => 'Bandung',
                'pic' => 'Tan Lucas Adnan Soen Hwat',
                'lat' => NULL,
                'long' => NULL,
                'description' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 5,
                'client_id' => 1,
                'name' => 'Location A1 - 4',
                'address' => 'Bandung',
                'pic' => 'Tan Lunardi',
                'lat' => NULL,
                'long' => NULL,
                'description' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 6,
                'client_id' => 2,
                'name' => 'Location B1 - 1',
                'address' => 'Jakarta',
                'pic' => 'Hartono Gunawan',
                'lat' => NULL,
                'long' => NULL,
                'description' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 7,
                'client_id' => 2,
                'name' => 'Location B1 - 2',
                'address' => 'Jakarta',
                'pic' => 'Hartono Sundoro Hosea',
                'lat' => NULL,
                'long' => NULL,
                'description' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 8,
                'client_id' => 2,
                'name' => 'Location B1 - 3',
                'address' => 'Jakarta',
                'pic' => 'Haruhiko',
                'lat' => NULL,
                'long' => NULL,
                'description' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 9,
                'client_id' => 2,
                'name' => 'Location B1 - 4',
                'address' => 'Jakarta',
                'pic' => 'Harun Ibrahim Tajuddin Nur',
                'lat' => NULL,
                'long' => NULL,
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
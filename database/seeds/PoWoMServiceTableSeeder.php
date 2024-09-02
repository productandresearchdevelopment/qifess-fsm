<?php

use Illuminate\Database\Seeder;

class PoWoMServiceTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('po_wo_m_service')->delete();
        
        \DB::table('po_wo_m_service')->insert(array (
            0 => 
            array (
                'id' => 1,
                'alias' => 'VSAT',
                'name' => 'VSAT',
                'color' => '0489e2',
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
                'alias' => 'CLEAN',
                'name' => 'CLEANING',
                'color' => '47ad0c',
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
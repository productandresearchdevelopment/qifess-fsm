<?php

use Illuminate\Database\Seeder;

class PoWoMActivityTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('po_wo_m_activity')->delete();
        
        \DB::table('po_wo_m_activity')->insert(array (
            0 => 
            array (
                'id' => 1,
                'alias' => 'INS',
                'name' => 'INSTALLATION',
                'color' => '2D8D0C',
                'description' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'alias' => 'CM',
                'name' => 'CORECTIVE MAINTENANCE',
                'color' => 'FF3300',
                'description' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'alias' => 'PM',
                'name' => 'PREVENTIVE MAINTENANCE',
                'color' => '0066CC',
                'description' => NULL,
            ),
        ));
        
        
    }
}
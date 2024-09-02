<?php

use Illuminate\Database\Seeder;

class PoWoMStatusDetailOptionTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('po_wo_m_status_detail_option')->delete();
        
        \DB::table('po_wo_m_status_detail_option')->insert(array (
            0 => 
            array (
                'id' => 1,
                'detail_id' => 8,
                'option' => '"Simple Combo 1"',
            ),
            1 => 
            array (
                'id' => 2,
                'detail_id' => 8,
                'option' => '"Simple Combo 2"',
            ),
            2 => 
            array (
                'id' => 3,
                'detail_id' => 8,
                'option' => '"Simple Combo 3"',
            ),
            3 => 
            array (
                'id' => 4,
                'detail_id' => 12,
                'option' => '{"name": "Singgalang", "age": 1}',
            ),
            4 => 
            array (
                'id' => 5,
                'detail_id' => 12,
                'option' => '{"name": "Rinjani", "age": 7}',
            ),
            5 => 
            array (
                'id' => 6,
                'detail_id' => 12,
                'option' => '{"name": "Sanggara", "age": 5}',
            ),
        ));
        
        
    }
}
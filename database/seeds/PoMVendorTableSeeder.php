<?php

use Illuminate\Database\Seeder;

class PoMVendorTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('po_m_vendor')->delete();

        \DB::table('po_m_vendor')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'PT - Vendor 1',
                'alias' => 'VEN1',
                'color' => NULL,
                'address' => 'Jakarta',
                'phone' => '021',
                'email' => 'ven1@mail.com',
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
                'name' => 'PT - Vendor 2',
                'alias' => 'VEN2',
                'color' => NULL,
                'address' => 'Bandung',
                'phone' => '022',
                'email' => 'ven2@mail.com',
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

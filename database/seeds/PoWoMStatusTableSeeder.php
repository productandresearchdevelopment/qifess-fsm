<?php

use Illuminate\Database\Seeder;

class PoWoMStatusTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('po_wo_m_status')->delete();
        
        \DB::table('po_wo_m_status')->insert(array (
            0 => 
            array (
                'id' => 1110,
                'type' => 0,
                'show_on' => NULL,
                'roles' => '[1,10,100,210]',
                'activities' => '[1]',
                'name' => 'OPEN',
                'alias' => 'OPEN',
                'color' => '04C813',
                'description' => NULL,
            ),
            1 => 
            array (
                'id' => 1210,
                'type' => 1,
                'show_on' => '[1110]',
                'roles' => '[1,10,100,210,220,1100]',
                'activities' => '[1]',
                'name' => 'ASSIGN FIELDTECH',
                'alias' => 'ASGN',
                'color' => '336600',
                'description' => NULL,
            ),
            2 => 
            array (
                'id' => 1310,
                'type' => 1,
                'show_on' => '[1210]',
                'roles' => '[1,10,100,210,220,1100,1110]',
                'activities' => '[1]',
                'name' => 'ARRIVAL',
                'alias' => 'ARVL',
                'color' => '6600CC',
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 1320,
                'type' => 1,
                'show_on' => '[1310]',
                'roles' => '[1,10,100,210,220,1100,1110]',
                'activities' => '[1]',
                'name' => 'SURVEI',
                'alias' => 'SURV',
                'color' => '663399',
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 1410,
                'type' => 1,
                'show_on' => '[1320]',
                'roles' => '[1,10,100,210,220,1100,1110]',
                'activities' => '[1]',
                'name' => 'INSTALLATION',
                'alias' => 'INST',
                'color' => '3399FF',
                'description' => NULL,
            ),
            5 => 
            array (
                'id' => 1420,
                'type' => 1,
                'show_on' => '[1410]',
                'roles' => '[1,10,100,210,220,1100,1110]',
                'activities' => '[1]',
                'name' => 'POINTING',
                'alias' => 'POIN',
                'color' => '3366CC',
                'description' => NULL,
            ),
            6 => 
            array (
                'id' => 1430,
                'type' => 1,
                'show_on' => '[1420]',
                'roles' => '[1,10,100,210,220,1100,1110]',
                'activities' => '[1]',
                'name' => 'PULLING',
                'alias' => 'PULL',
                'color' => '3300CC',
                'description' => NULL,
            ),
            7 => 
            array (
                'id' => 1440,
                'type' => 1,
                'show_on' => '[1430]',
                'roles' => '[1,10,100,210,220,1100,1110]',
                'activities' => '[1]',
                'name' => 'INDOOR INSTALLATION',
                'alias' => 'INDR',
                'color' => '009999',
                'description' => NULL,
            ),
            8 => 
            array (
                'id' => 1450,
                'type' => 1,
                'show_on' => '[1440]',
                'roles' => '[1,10,100,210,220,1100,1110]',
                'activities' => '[1]',
                'name' => 'COMMISIONING',
                'alias' => 'COMS',
                'color' => '006666',
                'description' => NULL,
            ),
            9 => 
            array (
                'id' => 1510,
                'type' => 1,
                'show_on' => '[1450]',
                'roles' => '[1,10,100,210,220]',
                'activities' => '[1]',
                'name' => 'APROVAL NOC',
                'alias' => 'NOC',
                'color' => 'FFCC33',
                'description' => NULL,
            ),
            10 => 
            array (
                'id' => 1520,
                'type' => 1,
                'show_on' => '[1510]',
                'roles' => '[1,10,100,210]',
                'activities' => '[1]',
                'name' => 'APPROVAL PM',
                'alias' => 'APRV',
                'color' => 'FF9900',
                'description' => NULL,
            ),
            11 => 
            array (
                'id' => 1610,
                'type' => 1,
                'show_on' => '[1520]',
                'roles' => '[1,10,100,210,220,1100,1110]',
                'activities' => '[1]',
                'name' => 'DOCUMENT BAST',
                'alias' => 'BAST',
                'color' => 'FF3300',
                'description' => NULL,
            ),
            12 => 
            array (
                'id' => 1810,
                'type' => 2,
                'show_on' => '[1610]',
                'roles' => '[1,10,100,210]',
                'activities' => '[1]',
                'name' => 'APPROVAL CLOSING',
                'alias' => 'CLOSE',
                'color' => '990000',
                'description' => NULL,
            ),
            13 => 
            array (
                'id' => 2110,
                'type' => 0,
                'show_on' => NULL,
                'roles' => '[1,10,100,210]',
                'activities' => '[2,3]',
                'name' => 'OPEN',
                'alias' => 'OPEN',
                'color' => '04C813',
                'description' => NULL,
            ),
            14 => 
            array (
                'id' => 2210,
                'type' => 1,
                'show_on' => '[2110]',
                'roles' => '[1,10,100,210,220,1100,1110]',
                'activities' => '[2,3]',
                'name' => 'ARRIVAL',
                'alias' => 'ARVL',
                'color' => '6600CC',
                'description' => NULL,
            ),
            15 => 
            array (
                'id' => 2310,
                'type' => 1,
                'show_on' => '[2210]',
                'roles' => '[1,10,100,210,220,1100,1110]',
                'activities' => '[2,3]',
                'name' => 'HANDLING',
                'alias' => 'HANDL',
                'color' => '3399FF',
                'description' => NULL,
            ),
            16 => 
            array (
                'id' => 2810,
                'type' => 2,
                'show_on' => '[2310]',
                'roles' => '[1,10,100,210,220,1100,1110]',
                'activities' => '[2,3]',
                'name' => 'CLOSING',
                'alias' => 'CLOSE',
                'color' => '990000',
                'description' => NULL,
            ),
        ));
        
        
    }
}
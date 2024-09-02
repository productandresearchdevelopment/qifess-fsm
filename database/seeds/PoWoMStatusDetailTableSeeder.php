<?php

use Illuminate\Database\Seeder;

class PoWoMStatusDetailTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('po_wo_m_status_detail')->delete();

        \DB::table('po_wo_m_status_detail')->insert(array (
            0 =>
            array (
                'id' => 1,
                'status_id' => 1110,
                'type' => 'text',
                'property' => NULL,
                'name' => 'Text (text)',
                'group' => NULL,
                'required' => 0,
                'default' => NULL,
                'sort' => 5,
                'triger' => NULL,
                'description' => NULL,
            ),
            1 =>
            array (
                'id' => 5,
                'status_id' => 1110,
                'type' => 'date',
                'property' => NULL,
                'name' => 'Date',
                'group' => 'DateTime',
                'required' => 0,
                'default' => NULL,
                'sort' => 1,
                'triger' => NULL,

                'description' => NULL,
            ),
            2 =>
            array (
                'id' => 6,
                'status_id' => 1110,
                'type' => 'time',
                'property' => NULL,
                'name' => 'Time',
                'group' => 'DateTime',
                'required' => 0,
                'default' => NULL,
                'sort' => 2,
                'triger' => NULL,

                'description' => NULL,
            ),
            3 =>
            array (
                'id' => 7,
                'status_id' => 1110,
                'type' => 'datetime',
                'property' => NULL,
                'name' => 'DateTime',
                'group' => 'DateTime',
                'required' => 0,
                'default' => NULL,
                'sort' => 3,
                'triger' => NULL,

                'description' => NULL,
            ),
            4 =>
            array (
                'id' => 8,
                'status_id' => 1110,
                'type' => 'combo',
                'property' => NULL,
                'name' => 'Simple Combo',
                'group' => 'ComboBox',
                'required' => 0,
                'default' => NULL,
                'sort' => 1,
                'triger' => NULL,

                'description' => NULL,
            ),
            5 =>
            array (
                'id' => 9,
                'status_id' => 1110,
                'type' => 'number',
                'property' => NULL,
                'name' => 'Number',
                'group' => 'Number',
                'required' => 0,
                'default' => NULL,
                'sort' => 1,
                'triger' => NULL,

                'description' => NULL,
            ),
            6 =>
            array (
                'id' => 10,
                'status_id' => 1110,
                'type' => 'number',
                'property' => '"currency"',
                'name' => 'Currency',
                'group' => 'Number',
                'required' => 0,
                'default' => NULL,
                'sort' => 2,
                'triger' => NULL,

                'description' => NULL,
            ),
            7 =>
            array (
                'id' => 11,
                'status_id' => 1110,
                'type' => 'check',
                'property' => NULL,
                'name' => 'Check',
                'group' => NULL,
                'required' => 0,
                'default' => NULL,
                'sort' => 4,
                'triger' => NULL,
                'description' => NULL,
            ),
            8 =>
            array (
                'id' => 12,
                'status_id' => 1110,
                'type' => 'combo',
                'property' => '{
"displayField": "name",
"displayTpl": "{name} ({age})",
"mapping": [
{"key": "name", "name": "Nama"},
{"key": "age", "name": "Umur"}
]
}',
                'name' => 'Combo Custom',
                'group' => 'ComboBox',
                'required' => 0,
                'default' => NULL,
                'sort' => 2,
                'triger' => NULL,

                'description' => NULL,
            ),
            9 =>
            array (
                'id' => 13,
                'status_id' => 1110,
                'type' => 'textarea',
                'property' => NULL,
                'name' => 'TextArea',
                'group' => NULL,
                'required' => 0,
                'default' => '"WhatsApp Messenger, or simply WhatsApp, is an American freeware, cross-platform messaging and Voice over IP service owned by Facebook, Inc. It allows users to send text messages and voice messages, make voice and video calls, and share images, documents, user locations, and other media"',
                'sort' => 3,
                'triger' => NULL,

                'description' => NULL,
            ),
            10 =>
            array (
                'id' => 14,
                'status_id' => 1110,
                'type' => 'file',
                'property' => '{
"maxFile": 6,
"fileType": "*",
"maxWidth": 1024,
"maxHeight": 768,
"autoResize": true
}',
                'name' => 'File Multiple',
                'group' => NULL,
                'required' => 0,
                'default' => NULL,
                'sort' => 1,
                'triger' => NULL,

                'description' => 'Property
(0) : Upload File Tidak Dibatasi
(1) : Single File
(2...dst) : jumlah maximum file',
            ),
            11 =>
            array (
                'id' => 15,
                'status_id' => 1110,
                'type' => 'combo',
                'property' => '{
"valueField": "id",
"displayField": "name",
"displayTpl": "{name} ({alias})",
"mapping": [
{"key": "name", "name": "Nama"},
{"key": "alias", "name": "Kode"}
]
}',
                'name' => 'Combo Ajax',
                'group' => 'ComboBox',
                'required' => 0,
                'default' => NULL,
                'sort' => 3,
                'triger' => NULL,

                'description' => NULL,
            ),
            12 =>
            array (
                'id' => 16,
                'status_id' => 1110,
                'type' => 'file',
                'property' => '{
"maxFile": 1,
"fileType": "image/*",
"autoResize": true
}',
                'name' => 'File Single',
                'group' => NULL,
                'required' => 0,
                'default' => NULL,
                'sort' => 2,
                'triger' => NULL,

                'description' => NULL,
            ),
        ));


    }
}

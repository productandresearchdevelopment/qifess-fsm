<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthModuleTypeTable extends Migration {

	public function up()
	{
		Schema::create('auth_module_type', function(Blueprint $table)
		{
			$table->integer('id')->primary();
			$table->string('group', 20)->nullable();
			$table->string('name', 50)->nullable();
			$table->string('icon', 50)->nullable();
			$table->boolean('show_menu')->nullable()->default(0);
			$table->boolean('xurl')->nullable()->default(0);
			$table->boolean('xroute')->nullable()->default(0);
			$table->boolean('xauth')->nullable()->default(0);
			$table->boolean('xicon')->nullable()->default(0);
			$table->boolean('xdevice')->nullable()->default(0);
			$table->string('description')->nullable();
		});

        \DB::table('auth_module_type')->insert(array (
            0 =>
                array (
                    'id' => 100,
                    'group' => 'Menu',
                    'name' => 'Menu Directory',
                    'icon' => 'menu-square',
                    'show_menu' => 1,
                    'xurl' => 0,
                    'xroute' => 0,
                    'xauth' => 0,
                    'xicon' => 1,
                    'xdevice' => 1,
                    'description' => NULL,
                ),
            1 =>
                array (
                    'id' => 110,
                    'group' => 'Menu',
                    'name' => 'Route Menu',
                    'icon' => 'menu',
                    'show_menu' => 1,
                    'xurl' => 0,
                    'xroute' => 1,
                    'xauth' => 0,
                    'xicon' => 1,
                    'xdevice' => 1,
                    'description' => NULL,
                ),
            2 =>
                array (
                    'id' => 210,
                    'group' => NULL,
                    'name' => 'View',
                    'icon' => 'file',
                    'show_menu' => 0,
                    'xurl' => 0,
                    'xroute' => 1,
                    'xauth' => 0,
                    'xicon' => 0,
                    'xdevice' => 0,
                    'description' => NULL,
                ),
            3 =>
                array (
                    'id' => 310,
                    'group' => NULL,
                    'name' => 'Service',
                    'icon' => 'gear',
                    'show_menu' => 0,
                    'xurl' => 0,
                    'xroute' => 1,
                    'xauth' => 0,
                    'xicon' => 0,
                    'xdevice' => 0,
                    'description' => NULL,
                ),
            4 =>
                array (
                    'id' => 410,
                    'group' => NULL,
                    'name' => 'Auth',
                    'icon' => 'users',
                    'show_menu' => 0,
                    'xurl' => 0,
                    'xroute' => 0,
                    'xauth' => 1,
                    'xicon' => 0,
                    'xdevice' => 0,
                    'description' => NULL,
                ),
            5 =>
                array (
                    'id' => 510,
                    'group' => NULL,
                    'name' => 'Hidden Directory',
                    'icon' => 'folder',
                    'show_menu' => 0,
                    'xurl' => 0,
                    'xroute' => 0,
                    'xauth' => 0,
                    'xicon' => 0,
                    'xdevice' => 0,
                    'description' => NULL,
                ),
        ));
	}

	public function down()
	{
		Schema::drop('auth_module_type');
	}

}

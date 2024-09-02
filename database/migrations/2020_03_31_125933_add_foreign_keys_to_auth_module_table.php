<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAuthModuleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('auth_module', function(Blueprint $table)
		{
			$table->foreign('type_id', 'auth_module_ibfk_1')->references('id')->on('auth_module_type')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('parent', 'auth_module_ibfk_2')->references('id')->on('auth_module')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('auth_module', function(Blueprint $table)
		{
			$table->dropForeign('auth_module_ibfk_1');
			$table->dropForeign('auth_module_ibfk_2');
		});
	}

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAuthRoleModuleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('auth_role_module', function(Blueprint $table)
		{
			$table->foreign('module_id', 'auth_role_module_ibfk_1')->references('id')->on('auth_module')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('role_id', 'auth_role_module_ibfk_2')->references('id')->on('auth_role')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('auth_role_module', function(Blueprint $table)
		{
			$table->dropForeign('auth_role_module_ibfk_1');
			$table->dropForeign('auth_role_module_ibfk_2');
		});
	}

}

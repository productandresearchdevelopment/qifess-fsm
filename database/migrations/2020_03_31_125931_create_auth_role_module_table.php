<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthRoleModuleTable extends Migration {

	public function up()
	{
		Schema::create('auth_role_module', function(Blueprint $table)
		{
			$table->integer('role_id');
			$table->integer('module_id')->index('module_id');
			$table->primary(['role_id','module_id']);
		});
	}

	public function down()
	{
		Schema::drop('auth_role_module');
	}

}

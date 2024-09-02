<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAuthUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('auth_user', function(Blueprint $table)
		{
			$table->foreign('photo', 'auth_user_ibfk_2')->references('id')->on('uploads')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('role_id', 'auth_user_ibfk_3')->references('id')->on('auth_role')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('auth_user', function(Blueprint $table)
		{
			$table->dropForeign('auth_user_ibfk_2');
			$table->dropForeign('auth_user_ibfk_3');
		});
	}

}

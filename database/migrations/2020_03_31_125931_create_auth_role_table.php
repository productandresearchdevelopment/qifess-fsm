<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthRoleTable extends Migration {

	public function up()
	{
		Schema::create('auth_role', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 30)->nullable();
			$table->string('alias', 10)->nullable();
			$table->integer('home')->nullable()->index('home');
			$table->char('color', 6)->nullable();
			$table->string('description', 50)->nullable();
			$table->text('properties')->nullable();
			$table->char('created_by', 36)->nullable();
			$table->char('updated_by', 36)->nullable();
			$table->char('deleted_by', 36)->nullable();
            $table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('auth_role');
	}

}

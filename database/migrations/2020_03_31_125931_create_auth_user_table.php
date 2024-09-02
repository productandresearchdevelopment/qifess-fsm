<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthUserTable extends Migration {

	public function up()
	{
		Schema::create('auth_user', function(Blueprint $table)
		{
			$table->char('id', 36)->primary();
            $table->integer('role_id')->nullable()->index('role_id');
            $table->integer('vendor_id')->nullable()->index('vendor_id');
            $table->integer('client_id')->nullable()->index('client_id');
            $table->integer('fieldtech_id')->nullable()->index('fieldtech_id');
            $table->string('activities')->nullable();
            $table->string('owners')->nullable();
            $table->string('username', 30)->nullable()->default('')->unique('username');
			$table->string('email', 150)->nullable()->default('')->unique('email');
            $table->string('password')->nullable();
            $table->string('token', 36)->nullable();
			$table->string('name', 50)->nullable();
			$table->string('phone', 30)->nullable();
			$table->char('photo', 36)->nullable()->index('photo');
			$table->string('description')->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->string('last_ip', 30)->nullable();
			$table->integer('last_module')->nullable();
			$table->string('last_url')->nullable();
			$table->dateTime('last_active')->nullable();
			$table->char('created_by', 36)->nullable()->index('created_by');
			$table->char('updated_by', 36)->nullable();
			$table->char('deleted_by', 36)->nullable();
            $table->timestamps();
			$table->softDeletes();
		});

	}

	public function down()
	{
		Schema::drop('auth_user');
	}

}

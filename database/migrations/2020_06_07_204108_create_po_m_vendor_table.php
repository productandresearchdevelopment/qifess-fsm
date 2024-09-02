<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoMVendorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('po_m_vendor', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 100)->nullable();
			$table->string('alias', 10)->nullable();
			$table->string('color', 6)->nullable();
			$table->string('address', 150)->nullable();
			$table->string('phone', 20)->nullable();
			$table->string('email', 150)->nullable();
			$table->string('description', 180)->nullable();
			$table->char('created_by', 36)->nullable();
			$table->char('updated_by', 36)->nullable();
			$table->char('deleted_by', 36)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('po_m_vendor');
	}

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoMFieldtechTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('po_m_fieldtech', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('vendor_id')->nullable()->index('vendor_id');
			$table->string('nik', 50)->nullable();
			$table->string('name', 100)->nullable();
			$table->string('address', 150)->nullable();
			$table->string('phone', 20)->nullable();
			$table->string('email', 150)->nullable();
			$table->char('photo', 36)->nullable();
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
		Schema::drop('po_m_fieldtech');
	}

}

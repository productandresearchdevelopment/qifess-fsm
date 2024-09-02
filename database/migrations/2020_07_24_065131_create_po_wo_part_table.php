<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoWoPartTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('po_wo_part', function(Blueprint $table)
		{
			$table->char('id', 36)->primary();
			$table->bigInteger('wo_id')->nullable()->index('wo_id');
            $table->string('type', 50)->nullable();
            $table->string('code', 100)->nullable();
            $table->string('name')->nullable();
            $table->string('serial', 100)->nullable();
            $table->string('model')->nullable();
            $table->string('description')->nullable();
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
		Schema::drop('po_wo_part');
	}

}

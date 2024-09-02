<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoWoPartFileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('po_wo_part_file', function(Blueprint $table)
		{
			$table->char('part_id', 36);
			$table->char('file_id', 36)->index('file_id');
			$table->primary(['part_id','file_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('po_wo_part_file');
	}

}

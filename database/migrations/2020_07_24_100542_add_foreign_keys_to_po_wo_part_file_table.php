<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPoWoPartFileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('po_wo_part_file', function(Blueprint $table)
		{
			$table->foreign('part_id', 'po_wo_part_file_ibfk_1')->references('id')->on('po_wo_part')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('file_id', 'po_wo_part_file_ibfk_2')->references('id')->on('uploads')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('po_wo_part_file', function(Blueprint $table)
		{
			$table->dropForeign('po_wo_part_file_ibfk_1');
			$table->dropForeign('po_wo_part_file_ibfk_2');
		});
	}

}

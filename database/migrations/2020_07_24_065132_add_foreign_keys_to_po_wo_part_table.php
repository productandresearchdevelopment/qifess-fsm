<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPoWoPartTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('po_wo_part', function(Blueprint $table)
		{
			$table->foreign('wo_id', 'po_wo_part_ibfk_1')->references('id')->on('po_wo')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('po_wo_part', function(Blueprint $table)
		{
			$table->dropForeign('po_wo_part_ibfk_1');
		});
	}

}

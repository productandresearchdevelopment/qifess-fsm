<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPoWoActionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('po_wo_action', function(Blueprint $table)
		{
			$table->foreign('wo_id', 'po_wo_action_ibfk_1')->references('id')->on('po_wo')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('status_id', 'po_wo_action_ibfk_2')->references('id')->on('po_wo_m_status')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('po_wo_action', function(Blueprint $table)
		{
			$table->dropForeign('po_wo_action_ibfk_1');
			$table->dropForeign('po_wo_action_ibfk_2');
		});
	}

}

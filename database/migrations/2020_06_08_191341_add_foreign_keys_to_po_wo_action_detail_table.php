<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPoWoActionDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('po_wo_action_detail', function(Blueprint $table)
		{
			$table->foreign('action_id', 'po_wo_action_detail_ibfk_1')->references('id')->on('po_wo_action')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('detail_id', 'po_wo_action_detail_ibfk_2')->references('id')->on('po_wo_m_status_detail')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('po_wo_action_detail', function(Blueprint $table)
		{
			$table->dropForeign('po_wo_action_detail_ibfk_1');
			$table->dropForeign('po_wo_action_detail_ibfk_2');
		});
	}

}

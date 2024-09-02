<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPoWoMStatusDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('po_wo_m_status_detail', function(Blueprint $table)
		{
			$table->foreign('status_id', 'po_wo_m_status_detail_ibfk_1')->references('id')->on('po_wo_m_status')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('po_wo_m_status_detail', function(Blueprint $table)
		{
			$table->dropForeign('po_wo_m_status_detail_ibfk_1');
		});
	}

}

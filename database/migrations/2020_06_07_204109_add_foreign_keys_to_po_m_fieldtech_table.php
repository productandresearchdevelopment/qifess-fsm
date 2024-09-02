<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPoMFieldtechTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('po_m_fieldtech', function(Blueprint $table)
		{
			$table->foreign('vendor_id', 'po_m_fieldtech_ibfk_1')->references('id')->on('po_m_vendor')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('po_m_fieldtech', function(Blueprint $table)
		{
			$table->dropForeign('po_m_fieldtech_ibfk_1');
		});
	}

}

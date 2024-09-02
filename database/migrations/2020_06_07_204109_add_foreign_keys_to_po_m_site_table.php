<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPoMSiteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('po_m_site', function(Blueprint $table)
		{
			$table->foreign('client_id', 'po_m_site_ibfk_1')->references('id')->on('po_m_client')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('po_m_site', function(Blueprint $table)
		{
			$table->dropForeign('po_m_site_ibfk_1');
		});
	}

}

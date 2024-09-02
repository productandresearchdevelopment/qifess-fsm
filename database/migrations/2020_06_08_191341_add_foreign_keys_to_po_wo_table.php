<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPoWoTable extends Migration {

	public function up()
	{
		Schema::table('po_wo', function(Blueprint $table)
		{
			$table->foreign('last_action', 'po_wo_ibfk_1')->references('id')->on('po_wo_action')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('site_id', 'po_wo_ibfk_2')->references('id')->on('po_m_site')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('activity_id', 'po_wo_ibfk_3')->references('id')->on('po_wo_m_activity')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('vendor_id', 'po_wo_ibfk_4')->references('id')->on('po_m_vendor')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('fieldtech_id', 'po_wo_ibfk_5')->references('id')->on('po_m_fieldtech')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('service_id', 'po_wo_ibfk_6')->references('id')->on('po_wo_m_service')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('owner_id', 'po_wo_ibfk_7')->references('id')->on('po_m_owner')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('remove_site_id', 'po_wo_ibfk_8')->references('id')->on('po_m_site')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}

	public function down()
	{
		Schema::table('po_wo', function(Blueprint $table)
		{
			$table->dropForeign('po_wo_ibfk_1');
			$table->dropForeign('po_wo_ibfk_2');
			$table->dropForeign('po_wo_ibfk_3');
			$table->dropForeign('po_wo_ibfk_4');
			$table->dropForeign('po_wo_ibfk_5');
            $table->dropForeign('po_wo_ibfk_6');
            $table->dropForeign('po_wo_ibfk_7');
            $table->dropForeign('po_wo_ibfk_8');
		});
	}

}

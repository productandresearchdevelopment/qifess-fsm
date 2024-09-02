<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPoMFieldtechAttachmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('po_m_fieldtech_attachment', function(Blueprint $table)
		{
			$table->foreign('file_id', 'po_m_fieldtech_attachment_ibfk_1')->references('id')->on('uploads')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('employ_id', 'po_m_fieldtech_attachment_ibfk_2')->references('id')->on('po_m_fieldtech')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('po_m_fieldtech_attachment', function(Blueprint $table)
		{
			$table->dropForeign('po_m_fieldtech_attachment_ibfk_1');
			$table->dropForeign('po_m_fieldtech_attachment_ibfk_2');
		});
	}

}

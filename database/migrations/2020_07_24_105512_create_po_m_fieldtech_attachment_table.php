<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoMFieldtechAttachmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('po_m_fieldtech_attachment', function(Blueprint $table)
		{
			$table->integer('employ_id')->index('detail_id');
			$table->char('file_id', 36)->index('file_id');
			$table->primary(['employ_id','file_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('po_m_fieldtech_attachment');
	}

}

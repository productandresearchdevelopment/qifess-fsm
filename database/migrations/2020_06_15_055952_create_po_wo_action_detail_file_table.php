<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoWoActionDetailFileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('po_wo_action_detail_file', function(Blueprint $table)
		{
			$table->char('detail_id', 36)->index('detail_id');
			$table->char('file_id', 36)->index('file_id');
			$table->primary(['detail_id','file_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('po_wo_action_detail_file');
	}

}

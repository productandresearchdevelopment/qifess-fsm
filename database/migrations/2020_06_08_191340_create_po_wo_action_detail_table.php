<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoWoActionDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('po_wo_action_detail', function(Blueprint $table)
		{
			$table->char('id', 36)->primary();
			$table->char('action_id', 36)->nullable()->index('action_id');
			$table->integer('detail_id')->nullable()->index('wo_id');
			$table->text('value', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('po_wo_action_detail');
	}

}

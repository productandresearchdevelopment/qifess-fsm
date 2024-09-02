<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoWoActionTable extends Migration {

	public function up()
	{
		Schema::create('po_wo_action', function(Blueprint $table)
		{
			$table->char('id', 36)->primary();
			$table->bigInteger('wo_id')->nullable()->index('wo_id');
			$table->integer('status_id')->nullable()->index('status_id');
			$table->string('note')->nullable();
			$table->float('lat', 10, 0)->nullable();
			$table->float('long', 10, 0)->nullable();
			$table->char('created_by', 36)->nullable();
			$table->char('updated_by', 36)->nullable();
			$table->char('deleted_by', 36)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('po_wo_action');
	}

}

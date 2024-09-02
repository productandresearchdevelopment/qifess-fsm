<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoWoMActivityTable extends Migration {

	public function up()
	{
		Schema::create('po_wo_m_activity', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('alias', 10)->nullable();
			$table->string('name', 50)->nullable();
			$table->string('color', 6)->nullable();
            $table->boolean('site_on')->default(1);
            $table->boolean('site_off')->default(0);
			$table->string('description', 150)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('po_wo_m_activity');
	}

}

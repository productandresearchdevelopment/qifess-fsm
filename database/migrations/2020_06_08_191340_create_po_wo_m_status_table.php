<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoWoMStatusTable extends Migration {

	public function up()
	{
		Schema::create('po_wo_m_status', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('type')->nullable()->comment('0: Open, 1 OnProcess, 2 Close');
            $table->string('show_on')->nullable()->comment('Array [status_id], Example: [110,210]');
            $table->string('roles')->nullable();
            $table->string('send_email_roles')->nullable();
            $table->string('activities')->nullable();
            $table->string('name', 30)->nullable();
			$table->string('alias', 10)->nullable();
			$table->string('color', 6)->nullable();
			$table->string('description')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('po_wo_m_status');
	}

}

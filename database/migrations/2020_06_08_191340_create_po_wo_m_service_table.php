<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoWoMServiceTable extends Migration {

	public function up()
	{
		Schema::create('po_wo_m_service', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('alias', 10)->nullable();
			$table->string('name', 50)->nullable();
			$table->string('color', 6)->nullable();
			$table->string('description', 150)->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('po_wo_m_service');
	}

}

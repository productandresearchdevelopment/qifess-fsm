<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoWoMStatusDetailTable extends Migration {

	public function up()
	{
		Schema::create('po_wo_m_status_detail', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('status_id')->nullable()->index('status_id');
			$table->string('type', 20)->nullable();
            $table->string('property')->nullable();
            $table->string('name', 30)->nullable();
            $table->string('group', 50)->nullable();
            $table->boolean('required')->nullable()->default(0);
			$table->text('default')->nullable();
            $table->integer('sort')->nullable();
            $table->string('triger')->nullable();
			$table->string('description')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('po_wo_m_status_detail');
	}

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoWoMStatusDetailOptionTable extends Migration {

	public function up()
	{
		Schema::create('po_wo_m_status_detail_option', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('detail_id')->nullable()->index('detail_id');
			$table->text('option')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('po_wo_m_status_detail_option');
	}

}

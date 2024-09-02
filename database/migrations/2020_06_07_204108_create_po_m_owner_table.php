<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoMOwnerTable extends Migration {

	public function up()
	{
		Schema::create('po_m_owner', function(Blueprint $table){
			$table->integer('id', true)->primary();
            $table->string('name', 100)->nullable();
            $table->string('alias', 10)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
		});
	}


	public function down()
	{
		Schema::drop('po_m_owner');
	}

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUploadsTable extends Migration {

	public function up()
	{
		Schema::create('uploads', function(Blueprint $table)
		{
			$table->char('id', 36)->primary();
			$table->string('filename')->nullable();
			$table->string('category', 50)->nullable()->index('category');
			$table->string('type', 50)->nullable();
			$table->string('mime', 50)->nullable();
			$table->string('extension', 10)->nullable();
			$table->float('size', 10, 0)->nullable();
			$table->string('origin')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('uploads');
	}

}

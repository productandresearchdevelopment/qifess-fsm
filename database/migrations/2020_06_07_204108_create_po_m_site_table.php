<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoMSiteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('po_m_site', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('link_id')->nullable()->index('link_id');
			$table->integer('client_id')->nullable()->index('client_id');
			$table->string('name', 100)->nullable();
			$table->string('terminal_name', 50)->nullable();
			$table->string('beam', 50)->nullable();
			$table->string('airmac', 50)->nullable();
			$table->string('serial_number', 50)->nullable();
			$table->integer('service_id')->nullable();
			$table->string('address', 150)->nullable();
			$table->string('pic', 100)->nullable();
			$table->string('pic_phone', 30)->nullable();
			$table->string('pic_email')->nullable();
			$table->float('lat', 10, 0)->nullable();
			$table->float('long', 10, 0)->nullable();
			$table->string('description')->nullable();
			$table->integer('is_active')->nullable();
			$table->date('active_date')->nullable();
			$table->date('inactive_date')->nullable();
			$table->char('created_by', 36)->nullable();
			$table->char('updated_by', 36)->nullable();
			$table->char('deleted_by', 36)->nullable();
			





			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('po_m_site');
	}

}

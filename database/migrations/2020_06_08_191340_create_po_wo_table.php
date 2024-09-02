<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoWoTable extends Migration {

	public function up()
	{
		Schema::create('po_wo', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
            $table->integer('activity_id')->nullable()->index('activity_id');
            $table->integer('service_id')->nullable()->index('service_id');
            $table->integer('owner_id')->nullable()->index('owner_id');
            $table->integer('client_id')->nullable()->index('client_id');
            $table->bigInteger('site_id')->nullable()->index('site_id');
            $table->bigInteger('remove_site_id')->nullable()->index('remove_site_id');
            $table->integer('vendor_id')->nullable()->index('vendor_id');
			$table->integer('fieldtech_id')->nullable()->index('fieldtech_id');
			$table->string('no_wo')->nullable()->index('no_wo');
            $table->string('description')->nullable();
            $table->date('start_date')->nullable()->comment('Tanggal Open Sesuai Kontrak');
            $table->date('expire_date')->nullable()->comment('Target Penyelesaian Sesuai Kontrak');
            $table->date('close_date')->nullable()->comment('Tanggal Closing Work Order');
            $table->char('last_action', 36)->nullable()->index('last_action');
			$table->char('created_by', 36)->nullable();
			$table->char('updated_by', 36)->nullable();
			$table->char('deleted_by', 36)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('po_wo');
	}

}

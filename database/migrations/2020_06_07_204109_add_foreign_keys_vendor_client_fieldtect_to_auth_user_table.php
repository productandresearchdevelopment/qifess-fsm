<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysVendorClientFieldtectToAuthUserTable extends Migration {

	public function up()
	{
		Schema::table('auth_user', function(Blueprint $table)
		{
            $table->foreign('vendor_id', 'auth_user_ibfk_4')->references('id')->on('po_m_vendor')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('client_id', 'auth_user_ibfk_5')->references('id')->on('po_m_client')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('fieldtech_id', 'auth_user_ibfk_6')->references('id')->on('po_m_fieldtech')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}

	public function down()
	{
		Schema::table('auth_user', function(Blueprint $table)
		{
            $table->dropForeign('auth_user_ibfk_4');
            $table->dropForeign('auth_user_ibfk_5');
            $table->dropForeign('auth_user_ibfk_6');
		});
	}

}

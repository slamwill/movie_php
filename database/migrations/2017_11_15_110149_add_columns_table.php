<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('vpn_account')->nullable()->unique();
            $table->string('vpn_password')->nullable();
            $table->date('vpn_expired')->nullable();
        });

        Schema::table('user_recharges', function (Blueprint $table) {
            $table->integer('days');
            $table->date('vpn_expired')->nullable();
        });
	
	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
			$table->dropColumn('vpn_account');
			$table->dropColumn('vpn_password');
			$table->dropColumn('vpn_expired');
        });
        Schema::table('user_recharges', function (Blueprint $table) {
			$table->dropColumn('days');
			$table->dropColumn('vpn_expired');
        });

	}
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trans_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
			//$table->decimal('coins',9,2)->default(0);
			$table->integer('coins')->default(0);
            $table->integer('userCoins')->default(0);
            $table->tinyInteger('type')->default(0);
            $table->string('info')->nullable();
			$table->string('memo')->nullable();
//			$table->json('json')->nullable();
            $table->timestamps();
        });

        Schema::create('user_recharges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('order_no')->unique();
			$table->integer('coins')->default(0);
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
	
	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trans_logs');
        Schema::dropIfExists('user_recharges');
    }
}

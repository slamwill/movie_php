<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRechargeConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharge_configs', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('amount')->default(0);
            $table->string('title');
            $table->integer('days')->default(0);
            $table->string('description');
			$table->enum('enable', ['on', 'off'])->default('on');
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
        Schema::dropIfExists('recharge_configs');
    }
}

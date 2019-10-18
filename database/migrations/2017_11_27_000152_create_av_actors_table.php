<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvActorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('av_actors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('nickname')->nullable();
			$table->date('birthday')->nullable();
			$table->integer('height')->nullable();
			$table->enum('cup', ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'])->default('A');
            $table->string('image')->nullable();

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
        Schema::dropIfExists('av_actors');
    }
}

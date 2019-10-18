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
        Schema::create('av_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
			
//            $table->integer('duration')->nullable();
			
//			$table->integer('coins')->default(0);
//			$table->integer('days')->default(0);
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
        Schema::dropIfExists('av_videos');
    }
}

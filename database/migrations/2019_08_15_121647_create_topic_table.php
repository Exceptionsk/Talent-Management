<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic', function (Blueprint $table) {
           $table->bigIncrements('id');
           $table->unsignedBigInteger('course_id');
           $table->string('topic');
           $table->text('descriptions');
           $table->date('start_date');
           $table->date('end_date');
           $table->timestamps();
           $table->foreign('course_id')
                 ->references('id')->on('course')
                 ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_detail');
    }
}
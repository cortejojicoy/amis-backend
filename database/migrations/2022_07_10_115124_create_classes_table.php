<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->bigIncrements('class_id');
            $table->integer('course_id');
            $table->integer('term_id');
            $table->integer('parent_class_id')->nullable();
            $table->string('type', 5);
            $table->string('section', 5);
            $table->string('date', 20);
            $table->string('time', 20);
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
        Schema::dropIfExists('classes');
    }
};

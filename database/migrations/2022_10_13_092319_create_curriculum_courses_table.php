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
        Schema::create('curriculum_courses', function (Blueprint $table) {
            $table->id('curriculum_course_id');
            $table->integer('curriculum_id');
            $table->integer('course_id')->nullable();
            $table->string('year', 1);
            $table->string('sem', 1);
            $table->text('description')->nullable();
            $table->string('course_type', 20);
            $table->string('sub_type', 50);
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
        Schema::dropIfExists('curriculum_courses');
    }
};

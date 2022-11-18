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
        Schema::create('courses', function (Blueprint $table) {
            $table->id('course_id');
            $table->integer('sais_course_id');
            $table->string('title', 255);
            $table->string('type', 50)->nullable();
            $table->text('description');
            $table->string('course_code', 20);
            $table->string('sem_offered', 255)->nullable();
            $table->string('career', 20);
            $table->string('units', 100);
            $table->boolean('is_repeatable');
            $table->boolean('is_active');
            $table->string('campus', 255);
            $table->integer('equivalent')->nullable();
            $table->boolean('is_multiple_enrollment');
            $table->string('subject', 15);
            $table->string('course_number', 10);
            $table->string('contact_hours', 50);
            $table->string('grading', 10);
            $table->integer('tm_id')->nullable();
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
        Schema::dropIfExists('courses');
    }
};

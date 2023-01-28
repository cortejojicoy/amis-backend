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
        Schema::create('pcw_revision_courses', function (Blueprint $table) {
            $table->id();
            $table->integer('pwc_course_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->string('course_type')->nullable();
            $table->integer('units')->nullable();
            $table->string('year', 1)->nullable();
            $table->string('sem', 1)->nullable();
            $table->integer('term')->nullable();
            $table->string('action');
            $table->integer('pwc_revision_id');
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
        Schema::dropIfExists('pcw_revision_courses');
    }
};

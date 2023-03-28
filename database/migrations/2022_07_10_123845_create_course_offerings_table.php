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
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->bigIncrements('course_offerings_id')->from(20129);
            $table->string('institution', 10);
            $table->string('career', 10);
            $table->integer('term');
            $table->integer('course_id');
            $table->string('acad_group', 10);
            $table->string('acad_org', 10);
            $table->string('subject', 10);
            $table->string('catalog', 10);
            $table->string('course', 10);
            $table->string('descr', 100);
            $table->string('activity', 100);
            $table->string('component', 5);
            $table->integer('assoc')->nullable();
            $table->string('section', 10);
            $table->string('times', 20);
            $table->string('days', 10);
            $table->string('facil_id', 10);
            $table->integer('tot_enrl');
            $table->integer('cap_enrl');
            $table->integer('class_nbr');
            $table->string('mtg_start', 20)->nullable();
            $table->string('mtg_end', 20)->nullable();
            $table->string('mon', 1)->nullable();
            $table->string('tues', 1)->nullable();
            $table->string('wed', 1)->nullable();
            $table->string('thurs', 1)->nullable();
            $table->string('fri', 1)->nullable();
            $table->string('sat', 1)->nullable();
            $table->string('sun', 1)->nullable();
            $table->integer('id');
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('consent', 1);
            $table->boolean('prerog');
            $table->integer('offer_nbr')->nullable();
            $table->integer('topic_id')->nullable();
            $table->text('prereq_coi')->nullable();
            $table->string('class_type', 1)->nullable();
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
        Schema::dropIfExists('course_offerings');
    }
};

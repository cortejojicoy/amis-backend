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
        Schema::create('student_course_offers', function (Blueprint $table) {
            $table->integer('course_id');
            $table->integer('offer_nbr');
            $table->string('institution', 5);
            $table->string('acad_group', 10);
            $table->string('subject', 10);
            $table->string('catalog', 10);
            $table->string('approved', 1);
            $table->string('campus', 4);
            $table->string('acad_prog', 11);
            $table->string('career', 5);
            $table->string('split_owner', 1);
            $table->integer('rq_group');
            $table->string('typically_offer', 10);
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
        Schema::dropIfExists('student_course_offers');
    }
};

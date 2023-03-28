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
        Schema::create('mentor_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36);
            $table->integer('mentor_faculty_id');
            $table->string('mas_id', 15);
            $table->integer('faculty_id');
            $table->string('acad_group', 10);
            $table->string('acad_org', 10)->nullable();
            $table->string('name', 255);
            $table->string('program', 10);
            $table->string('student_status', 10);
            $table->string('mentor', 255);
            $table->string('role',10);
            $table->string('status', 10);


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
        Schema::dropIfExists('mentor_assignments');
    }
};

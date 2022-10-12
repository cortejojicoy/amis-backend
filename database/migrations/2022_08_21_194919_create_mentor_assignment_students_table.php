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
        Schema::create('mentor_assignment_students', function (Blueprint $table) {
            $table->id();
            $table->integer('sais_id');
            $table->string('name',255);
            $table->string('email',255);
            $table->string('program',10)->nullable();
            $table->string('acad_group',10);
            $table->string('status',10)->nullable();
            $table->integer('mentor_id');
            $table->string('mentor_name',255)->nullable();
            $table->string('mentor_role',10)->nullable();
            $table->string('mentor_status',10)->nullable();
            $table->integer('adviser')->default(0);
            $table->integer('endorsed')->default(0);
            $table->integer('approved')->default(0);
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
        Schema::dropIfExists('mentor_assignment_students');
    }
};

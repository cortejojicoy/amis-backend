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
        Schema::create('save_mentors', function (Blueprint $table) {
            $table->id();
            $table->string('mas_id', 36)->nullable();
            $table->string('actions', 20);
            $table->string('mentor_name', 255);
            $table->integer('mentor_role');
            $table->string('field_represented', 50)->nullable();
            $table->date('effectivity_start')->nullable();
            $table->date('effectivity_end')->nullable();
            $table->string('uuid', 36);
            $table->integer('faculty_id');
            $table->string('actions_status', 11);
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
        Schema::dropIfExists('save_mentors');
    }
};

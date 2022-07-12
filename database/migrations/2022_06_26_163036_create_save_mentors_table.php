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
            $table->string('actions', 20);
            $table->string('mentor_name', 255);
            $table->string('mentor_role', 255);
            $table->string('field_represented', 50);
            $table->date('effectivity_start');
            $table->date('effectivity_end');
            $table->integer('sais_id');
            $table->integer('mentor_id');
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

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
            $table->string('mas_id', 15)->primary();
            $table->integer('student_sais_id');
            $table->integer('mentor_id');
            $table->string('status', 20);
            $table->string('mentor_role', 50);
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
